# StudentFinanceTracker
A personal finance tracker for students, built with plain PHP, MySQL, HTML, CSS and vanilla JavaScript.

**University of Sri Jayewardenepura** <br>
**Web Technologies | Year 2 Semester 1 | Group Activity**

## Group Members

| Name | GitHub |
|------|--------|
| Sisuru Disnaka | [@SisuruDisnaka](https://github.com/SisuruDisnaka) |
| Imasha Minoli | [@ImashaMinoli](https://github.com/ImashaMinoli) |
| Navodya Rathnayake | [@NavodyaRathnayake](https://github.com/NavodyaRathnayake) |
| Raihan Muieen | [@raihanmuieendoesthings-droid](https://github.com/raihanmuieendoesthings-droid) |
| Sathyadhi Dahanayake | [@SathyadhiDahanayake](https://github.com/SathyadhiDahanayake) |
| Manuji Dilansa | [@ManujiDilansa](https://github.com/ManujiDilansa) |
| Amaya Thisera | [@Amayathisera](https://github.com/Amayathisera) |
| Gayasha Ranazinghe | [@GayashaRanazinghe](https://github.com/GayashaRanazinghe) |
| Gimna Kaushadi | [@gimnakaushadi-bot](https://github.com/gimnakaushadi-bot) |
| Tharushi Lakshani | [@tharushilakshani-tl](https://github.com/tharushilakshani-tl) |

## Features

- Register, log in, update profile and password
- Add, edit, delete and search income and expenses by month and category
- Monthly budgets per category with usage alerts
- Saving plans with progress tracking and history
- Recurring monthly income and expenses, added in one click
- Automatic budget and saving notifications
- Dashboard and reports

## Setup

1. Start **Apache** and **MySQL** (e.g. XAMPP) and copy the project into `htdocs/student-finance-tracker`.
2. Import `db/db.sql` in phpMyAdmin.
3. Check the credentials in `includes/settings.php`.
4. Open `http://localhost/student-finance-tracker/`.

Demo account: `demo@student.lk`/`password123`, or register your own.

## Structure

```
assets/          css and js
db/              db.sql (schema + demo data)
includes/        config.php, settings.php, header.php, footer.php
*.php            one page per feature
```

## Security

Prepared statements, escaped output, hashed passwords, and every query scoped to the logged-in user.