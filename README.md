# Red Flags Dashboard
<img width="3788" height="1829" alt="Screenshot 2026-01-22 110745" src="https://github.com/user-attachments/assets/e4478985-91ad-4dd1-9a4a-9ddb31f95b64" />


**Red Flags** είναι ένα μοντέρνο **Security Incident Dashboard** που εμφανίζει logs και στατιστικά από το API σας, με δυνατότητα φιλτραρίσματος, real-time ενημέρωσης και όμορφη απεικόνιση των δεδομένων. Το frontend είναι φτιαγμένο με **TailwindCSS** και **Alpine.js**, ενώ το backend API με **FastAPI**.

---

## Screenshot
<img width="3055" height="1882" alt="Screenshot 2026-01-22 110758" src="https://github.com/user-attachments/assets/42a4e4ee-7d25-4b12-915f-5cfddd75463a" />

<img width="3748" height="1321" alt="Screenshot 2026-01-22 110815" src="https://github.com/user-attachments/assets/61405d20-9e9f-47a0-817b-b53319be113a" />

---

## Features

- **Live Logs Tab:**  
  - Αναζητήστε logs με βάση λέξεις-κλειδιά.  
  - Φιλτράρετε μόνο **active** incidents.  
  - Χρωματική κωδικοποίηση ανά **severity** ή **anomaly**.  
  - Δυνατότητα επιλογής του αριθμού των logs που εμφανίζονται.  

- **Analytics Tab:**  
  - Σύνολο incidents.  
  - Incidents τελευταίων 24 ωρών.  
  - Top source hosts.  
  - Κατανομή incidents ανά severity και log type.  

- **Responsive Design:** Λειτουργεί σε desktop και mobile.  
- **Dark Mode Friendly:** Σκούρο theme για εύκολη ανάγνωση.  

---

## Installation

### Backend (FastAPI)

1. Clone το repo:
```bash
git clone https://github.com/georgekazz/redflags-ui.git
cd redflags-ui
