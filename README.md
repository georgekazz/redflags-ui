# Red Flags Dashboard
![redflagmain](https://github.com/user-attachments/assets/04a92f8b-69dd-4a29-a8d7-4be36ceae631)


**Red Flags** είναι ένα μοντέρνο **Security Incident Dashboard** που εμφανίζει logs και στατιστικά από το API σας, με δυνατότητα φιλτραρίσματος, real-time ενημέρωσης και όμορφη απεικόνιση των δεδομένων. Το frontend είναι φτιαγμένο με **TailwindCSS** και **Alpine.js**, ενώ το backend API με **FastAPI**.

---

## Screenshot
![logsreflag](https://github.com/user-attachments/assets/ccb14b47-aca2-42aa-be2b-29e1bf16b3ba)

![redflags-analytics](https://github.com/user-attachments/assets/8c80d004-5115-4208-afe3-e201cb692ba5)

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
