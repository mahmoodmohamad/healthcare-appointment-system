#!/usr/bin/env bash
# Usage: bash rename_roles.sh /path/to/laravel-project
# Physician -> Doctor | Secretary -> Receptionist (content + file/folder names)
set -euo pipefail
cd "${1:-.}"

DIRS=()
for d in app routes resources database config lang tests; do [ -d "$d" ] && DIRS+=("$d"); done

# 1) file contents
grep -rlEi 'physic|secretar' "${DIRS[@]}" 2>/dev/null | while read -r f; do
  sed -i \
    -e 's/Physicain/Doctor/g; s/physicain/doctor/g' \
    -e 's/PHYSICIANS/DOCTORS/g; s/PHYSICIAN/DOCTOR/g' \
    -e 's/Physicians/Doctors/g; s/physicians/doctors/g; s/Physician/Doctor/g; s/physician/doctor/g' \
    -e 's/SECRETARIES/RECEPTIONISTS/g; s/SECRETARY/RECEPTIONIST/g' \
    -e 's/Secretaries/Receptionists/g; s/secretaries/receptionists/g; s/Secretary/Receptionist/g; s/secretary/receptionist/g' \
    "$f"
done

# 2) file + folder names (deepest first)
find "${DIRS[@]}" -depth \( -iname '*physic*' -o -iname '*secretar*' \) | while read -r p; do
  dir=$(dirname "$p"); base=$(basename "$p")
  new=$(echo "$base" | sed \
    -e 's/Physicians/Doctors/g; s/physicians/doctors/g; s/Physician/Doctor/g; s/physician/doctor/g' \
    -e 's/Secretaries/Receptionists/g; s/secretaries/receptionists/g; s/Secretary/Receptionist/g; s/secretary/receptionist/g')
  mv "$p" "$dir/$new"
done
echo "Done."