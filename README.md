# MembersActivities Framework

The **MembersActivities Framework** is an application framework built on top of the **Controller Framework**. It provides reusable functionality for applications that manage members, activities, subscriptions, cost items, payments and related processes.

Version **1.0.30** is based on the current generation of the Controller Framework and is intended for use with PHP 8.3 and MySQL/MariaDB.

For a complete guide to developing a client application with the Members Activities Framework 1.0.30, see the **[Membersactivities Framework 1.0.30 — Client Application Developer Guide](https://samoscon.github.io/2026/09/16/membersactivities-Framework-Client-Application-Development-Guide.html)**.

For a basic example application, see the **[Membersactivities Framework 1.0.30 — Basic Example Guide](https://samoscon.github.io/2026/09/12/membersactivities-Framework-Basic-Example.html)**.

For a complete technical guidance, see the **[Membersactivities Framework 1.0.30 — Technical Guide](https://samoscon.github.io/2026/09/14/membersactivities-Framework-Technical-Guide.html)**.




---

## 1. Framework stack

The framework is structured as follows:

```text
MembersActivities Framework
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

### Required components

| Component            | Version / requirement                   |
| -------------------- | --------------------------------------- |
| PHP                  | 8.3 or compatible newer PHP 8.x version |
| Controller Framework | 1.0.31                                  |
| Database access      | PDO                                     |
| Database             | MySQL / MariaDB                         |
| Web server           | Apache or compatible PHP web server     |
| Composer             | Required for dependency management      |

### Optional components

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

The database schema contains the core entities:

```text
member
activity
costitem
subscription
payment
remember_tokens
mail_queue
```

The schema also defines the relevant relationships between these entities.

---

# 3. Database structure

The principal relationships are:

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
                       ┌────────┴────────┐
                       ▼                 ▼
                   costitem          payment
                       │
                       ▼
                    activity
```

Members can also be grouped:

```text
             Member / Group
                   │
          ┌────────┼────────┐
          ▼        ▼        ▼
       Member   Member   Member
```

The `member.parent_id` relationship is used for groups such as families.

Deleting a parent member/group does not delete the underlying members. Their `parent_id` is set to `NULL`.

Members with related subscriptions or payments cannot be deleted while those records exist.

A payment may be associated with multiple subscriptions. This allows, for example, one payment to cover several tickets for an activity.

---

# 4. Installation

## 4.1 Create the database

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

---

# 5. Install the framework with Composer

The MembersActivities Framework is installed through Composer.

The client application should declare the MembersActivities Framework as a Composer dependency.

The Controller Framework is a dependency of the MembersActivities Framework and must be available in the required version:

```text
samoscon/controller-framework 1.0.31
```

The MembersActivities Framework version for this release is:

```text
samoscon/membersactivities-framework 1.0.30
```

Run:

```bash
composer install
```

for a new installation based on the supplied `composer.lock`.

When updating an existing installation, use the appropriate Composer update procedure rather than manually replacing framework files.

---

# 6. Configuration

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

The framework should not depend on the current working directory when resolving application paths. Paths should be based on the application/framework configuration and filesystem locations.

---

# 7. Controller Framework

MembersActivities Framework 1.0.30 requires:

```text
Controller Framework 1.0.31
```

The Controller Framework provides the underlying MVC/controller infrastructure, including functionality for:

* request handling
* command resolution
* controllers
* rendering
* database access
* application registry
* exception handling
* audit tracing
* security-related functionality
* access token handling

Version 1.0.31 includes the `AccessToken` class used by the current MembersActivities Framework for protected application operations.

---

# 8. Error handling

Applications using MembersActivities should use the error handling facilities supplied by the Controller Framework.

Exceptions should not normally be handled by displaying debugging information directly to the browser.

Production applications should use the configured error handling and logging mechanisms.

For example, application code should avoid patterns such as:

```php
print_r($exception);
```

or:

```php
echo $exception->getTraceAsString();
```

as production error handling.

The Controller Framework provides centralized error handling through its `ErrorHandler`.

---

# 9. Security and access tokens

The Controller Framework provides the `AccessToken` class for operations that require an additional application-level access token.

MembersActivities client applications can use access tokens to protect URLs and commands that should not be directly accessible without additional authorization.

Access tokens should be treated as security credentials.

They should:

* be generated using cryptographically secure randomness;
* be sufficiently long;
* be transmitted only over HTTPS;
* not be unnecessarily exposed in logs;
* be validated before performing the protected operation;
* have an appropriate lifetime or invalidation mechanism where applicable.

The exact use of `AccessToken` is determined by the client application and the protected command or resource.

---

# 10. Authentication

Applications using the framework should run over HTTPS.

Authentication functionality uses modern password hashing and verification mechanisms supplied by the framework.

Applications should never store plaintext passwords.

Remember-me authentication uses selector/token pairs. The stored token value is hashed and should not be treated as a plaintext authentication credential.

---

# 11. Mollie integration

Mollie support is optional.

When enabled, Mollie can be used for online payments.

The client application is responsible for configuring the required Mollie API credentials and payment workflow.

Mollie payment processing normally involves a webhook/callback from Mollie to the client application.

The webhook must therefore be publicly reachable over HTTPS and must perform the appropriate authorization and validation before updating payment information.

Mollie is not required for applications that do not process online payments.

---

# 12. Google Wallet integration

Google Wallet support is optional.

When enabled, the client application can generate Google Wallet passes/tickets.

The required Google credentials and Wallet configuration are application-specific and must be supplied by the client application.

Google Wallet functionality is not required by the core MembersActivities Framework.

---

# 13. Mail queue

The framework provides a `mail_queue` table for asynchronous mail processing.

Messages can be inserted into the queue and processed by a scheduled command/cron job.

The queue supports the following statuses:

```text
pending
sending
sent
failed
```

The queue also keeps track of:

```text
attempts
created_at
started_at
sent_at
error
```

Client applications should configure a suitable cron job to process the queue.

The exact command and filesystem path depend on the client application's installation.

---

# 14. Cron jobs

Scheduled commands should be executed using the PHP CLI environment configured for the application.

The client application should use absolute filesystem paths when configuring cron jobs.

Do not assume that the current working directory of a cron process is the same as the web application's working directory.

For example:

```bash
php /absolute/path/to/application/index.php <command>
```

The exact command depends on the client application's command configuration.

---

# 15. Production environment

A production installation should have:

```text
PHP 8.3
        │
        ▼
Controller Framework 1.0.31
        │
        ▼
MembersActivities Framework 1.0.30
        │
        ▼
MySQL / MariaDB
```

Development and production environments should use appropriate error-handling and logging configurations.

Debug output should not be enabled in production.

---

# 16. Updating an existing installation

Before updating an existing client application:

1. Create a database backup.
2. Create a backup of the application files.
3. Verify the PHP version.
4. Verify the Controller Framework version.
5. Update Composer dependencies.
6. Check the application configuration.
7. Test database connectivity.
8. Test authentication.
9. Test the main member/activity/subscription workflows.
10. Test access-token protected operations.
11. Test payment functionality when Mollie is enabled.
12. Test ticket generation and Google Wallet functionality when enabled.
13. Check application and audit logs.

The database schema of an existing production application should not simply be replaced by the reference schema.

Schema changes to an existing installation should be performed through controlled database migrations or carefully prepared SQL updates.

---

# 17. New client application

For a new client application, the recommended procedure is:

```text
1. Create application
        │
        ▼
2. Install MembersActivities Framework 1.0.30
        │
        ▼
3. Install Controller Framework 1.0.31
        │
        ▼
4. Create MySQL/MariaDB database
        │
        ▼
5. Import reference database schema
        │
        ▼
6. Configure database connection
        │
        ▼
7. Configure application
        │
        ├── Mollie (optional)
        │
        └── Google Wallet (optional)
        │
        ▼
8. Configure web server
        │
        ▼
9. Configure cron jobs
        │
        ▼
10. Test application
```

The client application can then extend the framework with application-specific commands, models, views and business logic.

---

# 18. Version information

This README applies to:

```text
MembersActivities Framework 1.0.30
```

Required Controller Framework:

```text
Controller Framework 1.0.31
```

The 1.0.30 release follows the previous 1.0.29 release.

Controller Framework 1.0.31 is required because the current MembersActivities Framework uses the `AccessToken` functionality introduced in that release.

---

# 19. Support and customization

The MembersActivities Framework provides reusable framework functionality.

Each client application remains responsible for:

* application-specific configuration;
* application-specific business rules;
* application-specific database extensions;
* payment configuration;
* mail configuration;
* authentication/authorization policies;
* access-token configuration where required;
* web server configuration;
* cron configuration;
* production deployment.

The supplied database schema should be treated as the **reference schema for a new installation**. Client applications may extend the schema when their specific requirements demand additional entities or fields.

---

# 20. Summary

The current MembersActivities Framework is based on the following technology stack:

```text
┌──────────────────────────────────────────────┐
│       MembersActivities Framework 1.0.30    │
├──────────────────────────────────────────────┤
│ PHP 8.3                                      │
│ Controller Framework 1.0.31                  │
│ PDO                                          │
│ MySQL / MariaDB                              │
├──────────────────────────────────────────────┤
│ Optional integrations                        │
│   • Mollie                                   │
│   • Google Wallet                            │
└──────────────────────────────────────────────┘
```

The framework is designed to provide a modern PHP 8.x foundation for member, activity, subscription, payment and ticketing applications while allowing individual client applications to add their own business logic and integrations.
