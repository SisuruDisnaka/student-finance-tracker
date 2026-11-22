# Change Request Log

This document maintains a record of all change requests raised during the StudentFinance project, including the request details, assigned Pull Requests (PRs), reviewers, approval decisions, and implementation status.

## Change Request Register

| CR ID  | Date       | Change Requested                | Requested By         | PR  | Reviewed By          | Approval | Review Date |
| ------ | ---------- | ------------------------------- | -------------------- | --- | -------------------- | -------- | ----------- |
| CR-001 | 2026-11-11 | Add Login UI and Authentication | Sisuru Disnaka       | #1  | Imasha Minoli        | Merged   | 2026-11-16  |
| CR-002 | 2026-11-16 | Add profile page                | Manuji Dilansa       | #4  | Imasha Minoli        | Merged   | 2026-11-16  |
| CR-004 | 2026-11-17 | Add Header & Footer             | Sisuru Disnaka       | #6  | Amaya Thisera        | Merged   | 2026-11-18  |
| CR-010 | 2026-11-19 | Add monthly setup               | Gimna Kaushadi       | #7  | Amaya Thisera        | Merged   | 2026-11-22  |
| CR-005 | 2026-11-20 | Add budgets page                | Nethmi Navodya       | #12 | Manuji Dilansa       | Merged   | 2026-11-21  |
| CR-007 | 2026-11-18 | Add Register and Logout         | Sisuru Disnaka       | #9  | Nethmi Navodya       | Merged   | 2026-11-21  |
| CR-003 | 2026-11-22 | Add income page                 | Sathyadhi Dahanayake | #5  | Gimna Kaushadi       | Merged   | 2026-11-22  |
| CR-006 | 2026-11-21 | Add expenses page               | Gayasha Ranasinghe   | #7  | Sathyadhi Dahanayake | Merged   | 2026-11-22  |
| CR-009 | 2026-11-19 | Notification                    | Amaya Thhisera       | #11 | Gayasha Ranasinghe   | Merged   | 2026-11-22  |
| CR-008 | 2026-11-18 | Add Saving Plans                | Imasha Minoli        | #10 | Sisuru Disnaka       | Merged   | 2026-11-22  |
| CR-012 | 2026-11-26 | Add reports page                | Tharushi lakshani| #15 | Nethmi Navodya | Merged | 2026-11-22 |

## Approval Status Values

- **Submitted** – The change request has been submitted for review.
- **Approved** – The reviewer has approved the change request.
- **Rejected** – The reviewer has rejected the change request.
- **Merged** – The related Pull Request has been merged into the target branch.

## How to Update the Log

1. Add a new row whenever a change request is raised. Set its status to `Submitted`.
2. Once the Pull Request is reviewed, the reviewer must fill in the `Reviewed By` and `Review Date` columns.
3. Update the `Approval` column to either `Approved` or `Rejected` based on the review decision.
4. After the approved Pull Request is merged, update the `Approval` status to `Merged`.
