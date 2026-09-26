# MembersActivities Framework

The **MembersActivities Framework** is an application framework built on top of the **Controller Framework**. It provides reusable functionality for applications that manage members, activities, subscriptions, cost items, payments, tickets and related processes.

Version **1.1.0** is based on Controller Framework 1.0.31 and is intended for use with PHP 8.3 and MySQL/MariaDB.

For a complete guide to developing a client application with the MembersActivities Framework 1.1.0, see the **[MembersActivities Framework 1.1.0 — Client Application Developer Guide](https://samoscon.github.io/2026/09/16/membersactivities-Framework-Client-Application-Development-Guide.html)**.

For a basic example and instructions for creating a client application, see the **[MembersActivities Framework 1.1.0 — Basic Example Guide](https://samoscon.github.io/2026/09/26/membersactivities-Framework-Basic-Example.html)**.

For complete technical guidance, see the **[MembersActivities Framework 1.1.0 — Technical Guide](https://samoscon.github.io/2026/09/14/membersactivities-Framework-Technical-Guide.html)**.

> **Important:** The framework distribution no longer contains a complete `example` client application. The example client application structure is documented separately in the Basic Example Guide. The guide also provides links to the reference database SQL file and a ZIP archive containing the example client application file structure.

---

# 1. Framework stack

The framework is structured as follows:

```text
MembersActivities Framework 1.1.0
        │
        ├── PHP 8.3
        │
        ├── Controller Framework 1.0.31
        │
        ├── PDO
        │
        ├── MySQL / MariaDB
        │
        └── Optional integrations
                ├── Mollie
                └── Google Wallet
```

## Required components

| Component            | Version / requirement                   |
| -------------------- | --------------------------------------- |
| PHP                  | 8.3 or compatible newer PHP 8.x version |
| Controller Framework | 1.0.31                                  |
| Database access      | PDO                                     |
| Database             | MySQL / MariaDB                         |
| Web server           | Apache or compatible PHP web server     |
| Composer             | Required for dependency management      |

## Optional components

Depending on the client application, the following integrations can be installed and configured:

* Mollie for online payments
* Google Wallet for digital tickets

These integrations are not required for the core MembersActivities functionality.

---

# 2. Database

The MembersActivities Framework uses **PDO with a MySQL/MariaDB DSN**.

Oracle is **not** a supported database platform for this version of the framework.

The recommended database engine is:

```text
MariaDB 10.6+
```

MySQL versions compatible with the application's SQL requirements may also be used.

The reference database schema is supplied separately and is intended to provide a starting point for a **new client application database**.

The schema uses:

```text
InnoDB
utf8mb4
foreign key constraints
```

The core database entities include:

```text
member
activity
costitem
subscription
payment
ticket
ticket_scan
remember_tokens
mail_queue
```

The schema also defines the relevant relationships between these entities.

> **Reference schema:** The database SQL file is no longer distributed as part of an `example` directory inside the framework. It is available through the Basic Example Guide.

---

# 3. Database structure

The principal relationships include:

```text
                          ┌──────────────┐
                          │    member    │
                          └──────┬───────┘
                                 │
               ┌─────────────────┼─────────────────┐
               │                 │                 │
               ▼                 ▼                 ▼
           payment         subscription      remember_tokens
                                 │
                    ┌────────────┼────────────┐
                    │            │            │
                    ▼            ▼            ▼
                costitem      payment       ticket
                    │                         │
                    ▼                         ▼
                activity                  ticket_scan
```

The exact relationships depend on the application domain and the corresponding foreign-key definitions in the reference schema.

## Member hierarchy

Members can also be grouped:

```text
             Member / Group
                    │
             ┌──────┼──────┐
             ▼      ▼      ▼
          Member  Member  Member
```

The `member.parent_id` relationship can be used for groups such as families.

The parent-child relationship follows the Composite design principle: a parent Member and its child Members are instances of the same domain type.

Deleting a parent member/group does not delete the underlying members. Their `parent_id` is set to `NULL`.

Members with related subscriptions or payments cannot be deleted while those records exist.

---

# 4. Activity and Member Composite relationships

The framework supports recursive parent-child relationships for both Activities and Members.

## Activity hierarchy

```text
             Activity / Composite
                    │
             ┌──────┼──────┐
             ▼      ▼      ▼
         Activity Activity Activity
```

An `ActivityComposite` can contain child Activities.

This allows applications to represent structures such as:

* a concert project containing individual performances;
* a course containing multiple sessions;
* a program containing several activities.

## Member hierarchy

```text
               Member / Group
                    │
             ┌──────┼──────┐
             ▼      ▼      ▼
          Member  Member  Member
```

This can be used, for example, to represent a family or another member group.

Both relationships are based on the same general Composite principle: the parent and child objects belong to the same domain type.

---

# 5. Ticket model

MembersActivities 1.1.0 introduces a persistent Ticket domain model.

A Ticket represents an individual ticket associated with a subscription.

This is important because a subscription can contain multiple tickets. Individual Ticket records provide a stable identity for:

* ticket PDFs;
* QR codes;
* ticket scanning;
* seat assignments;
* Google Wallet passes;
* ticket status;
* ticket usage.

A Ticket contains information such as:

```text
id
description
classification
subscription_id
token
status
used
used_at
created_at
```

The default classification is normally:

```text
RGLR
```

The client application can provide a corresponding:

```text
Ticket_RGLR
```

type implementation.

---

# 6. Ticket scanning

MembersActivities 1.1.0 also provides a persistent `TicketScan` domain model.

A TicketScan records the result of an attempt to scan a ticket.

Typical information includes:

```text
id
description
classification
ticket_id
scanner
scanned_at
result
```

Typical scan results include:

```text
valid
already_used
cancelled
invalid
wrong_activity
```

The scanner flow is conceptually:

```text
Ticket QR code
      │
      ▼
Scanner application
      │
      ▼
Validate ticket_scan AccessToken
      │
      ▼
Find Ticket by token
      │
      ▼
Verify activity
      │
      ▼
Verify ticket status
      │
      ▼
Atomically claim Ticket
      │
      ├── accepted
      │
      └── rejected
             │
             ▼
        create TicketScan
```

The application must prevent two scanners from accepting the same ticket simultaneously.

A typical atomic ticket claim is based on:

```sql
UPDATE ticket
SET
    used = 1,
    used_at = CURRENT_TIMESTAMP
WHERE
    id = :ticket_id
    AND status = 'valid'
    AND used = 0;
```

The application should verify that exactly one row was changed before considering the ticket accepted.

---

# 7. Installation

## 7.1 Create the database

Create a new MySQL/MariaDB database for the client application.

The database should preferably use:

```text
utf8mb4
```

and the tables supplied in the reference schema use:

```text
ENGINE=InnoDB
```

Import the supplied reference database schema into the new database.

The reference schema is intended as a **database creation template**, not as a dump of production data.

It contains the table definitions, indexes and foreign key constraints, but no client-specific data.

The SQL file is available through the Basic Example Guide.

---

# 8. Install the framework with Composer

The MembersActivities Framework is installed through Composer.

The client application should declare the MembersActivities Framework as a Composer dependency.

The Controller Framework is a dependency of the MembersActivities Framework and must be available in the required version:

```text
samoscon/controller-framework 1.0.31
```

The MembersActivities Framework version for this release is:

```text
samoscon/membersactivities-framework 1.1.0
```

A client application can specify, for example:

```json
{
    "require": {
        "samoscon/membersactivities-framework": "^1.1.0"
    }
}
```

Run:

```bash
composer install
```

for a new installation based on the supplied `composer.lock`.

When updating an existing installation, use the appropriate Composer update procedure rather than manually replacing framework files.

---

# 9. Basic Example Application

The complete example client application is **not included in the MembersActivities Framework package**.

This is intentional.

The framework package contains the reusable framework functionality, while the client application remains separate.

The Basic Example Guide provides:

* instructions for creating a client application;
* the recommended client application directory structure;
* example Composer configuration;
* example configuration;
* example model specializations;
* example commands and CommandDecorators;
* activity and seat-map examples;
* payment and Mollie examples;
* Ticket and TicketScan examples;
* security examples;
* instructions for testing the application.

The guide also provides links to:

* `DatabaseSetup.sql`;
* a ZIP archive containing the example client application file structure and files.

See:

**[MembersActivities Framework 1.1.0 — Basic Example Guide](https://samoscon.github.io/2026/09/26/membersactivities-Framework-Basic-Example.html)**

This separation keeps the framework package focused on reusable framework code while still providing a complete reference for developers who want to start a new client application.

---

# 10. Configuration

The client application is responsible for supplying its own application configuration.

Database configuration must provide the information required to construct a PDO MySQL/MariaDB connection.

The database connection uses a DSN in the form:

```text
mysql:host=<host>;dbname=<database>;charset=utf8mb4
```

Typical configuration values include:

```text
DBHOST
DBNAME
DBUSER
DBPASSWORD
```

The exact configuration mechanism is determined by the client application.

The framework should not depend on the current working directory when resolving application paths. Paths should be based on the application/framew
