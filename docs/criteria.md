# Grading criteria

Translated from the course's grading document (Polish). If anything is unclear, the original wording is authoritative.
Each criterion has a "DuelLab" line pointing to where the project meets it.

## Hard requirement

The backend must be object-oriented. A backend written procedurally is graded 2.0 and no other criterion is scored.
Placing single functions in random classes does not count as object-oriented programming.

## Presentation

The project is presented during a lab, a consultation or another date agreed with the lecturer. Saying that a feature
exists is not enough: each criterion must be run, pointed to in the code, or documented in the repository.
During the demo the author explains the flow of a chosen request: from the interface and routing, through the
controller and application logic, to the database and the response.

## Scoring

Each of the 36 criteria gives:
- 1 point: implemented correctly, works and can be verified;
- 0.5 point: exists but is incomplete, unstable or poorly documented;
- 0 points: missing, not working, or cannot be presented.

Result = points / 36 × 100%.

| Points    | Result    | Grade |
|-----------|-----------|-------|
| 34–36     | 94–100%   | 5.0   |
| 29.5–33.5 | 81–93%    | 4.5   |
| 26–29     | 71–80%    | 4.0   |
| 22–25.5   | 61–70%    | 3.5   |
| 18.5–21.5 | 51–60%    | 3.0   |
| 0–18      | below 51% | 2.0   |

## Before submission

Check the project on a clean environment. The grader must be able to clone the repository, create `.env` from
`.env.example` and start the app by following `README.md`.

- [ ] The final version is on the `main` branch.
- [ ] The repository contains no passwords, tokens or production secrets.
- [ ] The app starts with Docker Compose.
- [ ] The database has a schema and sample data that make the demo possible.
- [ ] `README.md` contains the link to the deployed app and the demo account credentials.
- [ ] Tests, the WCAG report and all required materials are in the repository.

## Criteria

- [ ] **1. README documentation.** README contains a description of the problem and the app's features, setup instructions, requirements, demo data, screenshots of the most important screens, a description of the application flow, the architecture, the ERD, how to run the tests, and the deployment link. Screenshots show the real project, not just the prototype.
  - DuelLab: `README.md` is not created yet (`roadmap.md` → M7).
- [ ] **2. Docker.** The project starts reproducibly with `docker compose up --build`. The application and PostgreSQL containers communicate by service names, database data persists in a volume, and ports and variables are described. A full point requires running the project on a clean environment.
  - DuelLab: `architecture.md` → Components.
- [ ] **3. Application architecture.** The app has a clear separation of responsibilities, e.g. MVC or a separate frontend and backend. README contains a diagram or description of the layers and the direction of dependencies. A controller should not contain all of the SQL, HTML and business logic at once.
  - DuelLab: `architecture.md` → Layers.
- [ ] **4. Routing.** The router recognises HTTP methods and URL patterns. The project has a static route, a regex pattern, a path parameter with passing and validation of the id, and a useful query parameter. Invalid or non-existent identifiers end with a controlled 400 or 404 response.
  - DuelLab: `architecture.md` → Routes; status rules in `src/AGENTS.md` → Routes and errors.
- [ ] **5. Object-oriented backend.** The backend uses classes, objects, encapsulation, types and separation of responsibilities. Controllers, repositories or services cooperate through clearly defined interfaces. Procedural code is limited to the entry point and configuration.
  - DuelLab: `src/AGENTS.md` → Classes.
- [ ] **6. ERD diagram in Mermaid.** An up-to-date ERD is placed directly in `README.md` as a Mermaid `erDiagram` block. It shows tables, primary keys, foreign keys, important attributes and relationship cardinalities, and matches the real schema.
  - DuelLab: built from `architecture.md` → Data model.
- [ ] **7. Git.** The history shows systematic work: small logical commits, readable messages, and no single bulk commit with the whole project. The final, working version of all team members is merged into `main`.
  - DuelLab: `AGENTS.md` → Commits and finishing a task.
- [ ] **8. Delivering the topic.** The project matches the submitted topic, implements its most important process from start to end, and allows a coherent demo. Features cannot be only static mockups.
  - DuelLab: `product.md` → Main flow.
- [ ] **9. HTML.** Views use semantic, valid HTML. Repeated elements, e.g. the header, navigation and footer, are extracted into shared templates or components. Forms have labels, appropriate field types and clear error messages.
  - DuelLab: `public/AGENTS.md` → Views.
- [ ] **10. PostgreSQL.** The app really uses PostgreSQL. The connection is configured outside the code, queries are parameterised, and data is mapped in a controlled way. The student can point to a query executed during the demo.
  - DuelLab: `src/AGENTS.md` → Data, files and permissions.
- [ ] **11. Database complexity.** The schema has several related entities, correct keys, constraints and justified relations. Data is normalised, with no obvious redundancy or modification anomalies. Column types match the stored values.
  - DuelLab: `architecture.md` → Data model.
- [ ] **12. Database export to .sql.** The repository contains an up-to-date `.sql` file that recreates the schema and safe demo data. The import works on an empty database and contains no production personal data or secrets.
  - DuelLab: `roadmap.md` → M7.
- [ ] **13. PHP.** The code uses a modern, supported PHP version, type declarations, proper error handling and standard functions. It produces no warnings or errors during a typical scenario. The student understands the PHP code presented.
  - DuelLab: `AGENTS.md` → Code style.
- [ ] **14. JavaScript.** JavaScript implements real interface behaviour: client-side validation, event handling and deliberate DOM manipulation. The code is split into functions or modules and is not just one copied script.
  - DuelLab: `public/AGENTS.md` (JavaScript rules still TODO).
- [ ] **15. Fetch API (AJAX).** The app makes at least one justified GET and one POST request through the Fetch API without a full page reload. It handles JSON, HTTP statuses, the loading state, success and error, and updates the interface with the received data.
  - DuelLab: `architecture.md` → Routes, the two `/api/` endpoints.
- [ ] **16. Design.** The interface is consistent, readable and fits the topic. It uses consistent typography, palette, spacing and components. Styles live in CSS stylesheets, not repeated as random inline styles.
  - DuelLab: `public/AGENTS.md` → Styles and layout.
- [ ] **17. Responsiveness.** At least five important views work correctly on a phone, tablet and computer. There is no horizontal scrolling or overlapping elements. The project uses flexible layouts and justified media queries.
  - DuelLab: `public/AGENTS.md` → Styles and layout.
- [ ] **18. Dark/light mode.** The theme is set with the `data-theme` attribute on `<html>`, the user's choice is saved in localStorage, and the first choice respects `prefers-color-scheme`. A short script in `<head>` sets the theme before rendering and prevents FOUC (Flash of Unstyled Content).
  - DuelLab: `public/AGENTS.md` → Styles and layout.
- [ ] **19. WCAG compliance.** The interface can be used with a keyboard, focus is visible, images have alternatives, forms have labels, and contrast is sufficient. README contains a report from a tool, e.g. Lighthouse, axe or WAVE, with the date, the score, a screenshot and a description of the fixed problems.
  - DuelLab: `public/AGENTS.md` → Views; report in `roadmap.md` → M7.
- [ ] **20. Login.** The user logs in with data from the database. The password is verified without comparing plain text, wrong credentials return a safe message, and after login there is a correct redirect and the user's identity is restored.
  - DuelLab: `src/AGENTS.md` → Authentication and session.
- [ ] **21. User session.** The logged-in state is kept when moving between pages. The session id is regenerated after login, the cookie has the right settings, and client data is not treated as a trusted source of permissions.
  - DuelLab: `src/AGENTS.md` → Authentication and session.
- [ ] **22. User permissions.** The backend checks permissions before every protected operation. Hiding a button in HTML is not protection. Manually calling a forbidden route ends with a 401 or 403 response.
  - DuelLab: `src/AGENTS.md` → Data, files and permissions; Routes and errors.
- [ ] **23. User roles.** The app has at least two meaningful roles, e.g. USER and ADMIN. The roles have different capabilities, are stored in the database or mapped by the backend, and can be presented on separate accounts.
  - DuelLab: `product.md` → Roles.
- [ ] **24. Logout.** Logout invalidates the session data and deletes or expires the relevant cookie. Using the Back button or repeating a request to a protected route does not restore access.
  - DuelLab: `src/AGENTS.md` → Authentication and session.
- [ ] **25. Views, triggers, functions or transactions.** The database or repository uses at least one of: a view, a trigger, a PostgreSQL function or a transaction. The use must come from a real scenario, be in the export or a migration, and be shown during the demo.
  - DuelLab: `architecture.md` → Data model (view and transactions).
- [ ] **26. Referential actions.** The app runs queries that join related tables with JOIN, and foreign keys have deliberate ON DELETE and possibly ON UPDATE behaviour. The student can justify the choice of CASCADE, RESTRICT, SET NULL or the default behaviour.
  - DuelLab: `architecture.md` → Data model.
- [ ] **27. Security: Security Bingo.** Security Bingo is completed and documented. The project uses, among others, password hashing, parameterised queries, data validation, output encoding, server-side authorization, form protection and safe upload. README contains a table with evidence for each marked field.
  - DuelLab: rules in `src/AGENTS.md` and `public/AGENTS.md`; evidence table in `roadmap.md` → M7.
- [ ] **28. No code duplication.** Repeated logic is extracted into shared methods, classes, services, components or templates. The repository has no multiple copies of the same query, validation or large HTML fragment.
  - DuelLab: `AGENTS.md` → Code style.
- [ ] **29. Clean, readable code.** Names describe responsibility, methods are short, formatting is consistent, and dead code and debugging are removed. The directory structure is predictable. Comments explain decisions instead of repeating the code.
  - DuelLab: `AGENTS.md` → Code style.
- [ ] **30. Translations.** The interface supports at least Polish and English. Texts are not scattered across controllers and views but come from a translation catalogue or mechanism. Switching the language also covers validation and error messages.
  - DuelLab: `public/AGENTS.md` (translation rules still TODO).
- [ ] **31. Unit tests.** The repository contains runnable unit tests for important backend logic, including a valid and an invalid case. Tests are isolated, repeatable, pass, and have a documented command to run them.
  - DuelLab: `AGENTS.md` → Testing.
- [ ] **32. CRON.** The project has a justified recurring job, e.g. cleaning expired data or sending reminders. The schedule, command and how to test it are described. The job writes a result or log and is safe to re-run.
  - DuelLab: `architecture.md` → Scheduled jobs.
- [ ] **33. PHPMailer.** The app sends a useful e-mail through PHPMailer or an equivalent SMTP mechanism. Mail server settings come from the environment, errors are handled, and the local demo may use Mailpit.
  - DuelLab: `product.md` → Roles, Main flow and Other features (password reset, `.cfg` by e-mail, weekly summary).
- [ ] **34. Image upload.** The backend checks the size, the real MIME type and the allowed extension, gives the file a safe unique name, and stores it outside the application code or in a dedicated uploads directory. The database stores only a URL or a relative path, not the file content.
  - DuelLab: `product.md` → Other features (map radar); rules in `src/AGENTS.md` → Data, files and permissions.
- [ ] **35. Variables in .env or Key Vault.** Passwords, tokens, keys and environment-dependent configuration are not written in the code or committed. The repository contains a safe `.env.example` with the names of required variables. Production uses environment variables or a secret manager.
  - DuelLab: `AGENTS.md` → Security.
- [ ] **36. Deploy.** The app is available at a public address and works outside the author's computer. The deployed version matches the `main` branch, uses production configuration, and has a working database, uploads and required variables. The link and a short description of the deployment are in README.
  - DuelLab: `roadmap.md` → M3 (first deploy); hosting in Pending decisions.