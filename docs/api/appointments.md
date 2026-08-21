# Appointments API

The appointments API provides authenticated REST endpoints for booking, viewing, rescheduling, completing, and cancelling appointments.

## Base URL

```text
/api
```

All endpoints require a Sanctum bearer token:

```http
Authorization: Bearer {token}
Accept: application/json
```

## Authorization

| Role | List own appointments | View | Create | Update | Cancel |
| --- | --- | --- | --- | --- | --- |
| Administrator | All | All | Yes | Yes | Yes |
| Secretary | All | All | Yes | Yes | Yes |
| Physician | Assigned appointments | Assigned appointments | No | Assigned appointments | Assigned appointments |
| Patient | Own appointments | Own appointments | No | No | No |

## Endpoints

| Method | Endpoint | Purpose |
| --- | --- | --- |
| `GET` | `/api/appointments` | Return paginated appointments visible to the authenticated user. |
| `POST` | `/api/appointments` | Book a new appointment. |
| `GET` | `/api/appointments/{appointment}` | Return one authorized appointment. |
| `PUT/PATCH` | `/api/appointments/{appointment}` | Update an appointment or reschedule it. |
| `DELETE` | `/api/appointments/{appointment}` | Cancel an appointment by changing its status to `cancelled`. |

## List appointments

Optional query parameters are `status`, `date` (`YYYY-MM-DD`), and `page`.

```http
GET /api/appointments?status=scheduled&date=2026-08-21
```

The response is a Laravel pagination resource containing the appointment data and related patient, physician, secretary, and diagnosis summaries.

## Book an appointment

Only administrators and secretaries may create appointments.

```http
POST /api/appointments
Content-Type: application/json

{
  "patient_id": 12,
  "physician_id": 4,
  "appointment_date": "2026-08-25",
  "appointment_time": "10:30",
  "notes": "Initial consultation"
}
```

The API rejects dates before today, invalid times, unknown patients or physicians, and time slots already assigned to a non-cancelled appointment for the same physician. A successful request returns `201 Created`.

## Update or reschedule an appointment

Administrators, secretaries, and the assigned physician may update an appointment.

```http
PATCH /api/appointments/18
Content-Type: application/json

{
  "appointment_date": "2026-08-26",
  "appointment_time": "14:00",
  "status": "scheduled",
  "notes": "Moved at patient request"
}
```

Supported statuses are `scheduled`, `completed`, and `cancelled`.

## Cancel an appointment

Cancellation is intentionally implemented as a status change rather than a destructive delete:

```http
DELETE /api/appointments/18
```

```json
{
  "message": "Appointment cancelled successfully."
}
```

## Error responses

Validation errors return HTTP `422` with field-level messages. Unauthorized requests return HTTP `401`. Authenticated users without permission return HTTP `403`. Responses use JSON and do not expose passwords or session data.

## Implementation notes

The API is separated from the existing web controllers and uses dedicated Form Requests, an API Resource, route model binding, pagination, and explicit role-aware access checks. New PHP code follows PSR-12-compatible formatting and Laravel 9 conventions.
