CREATE TABLE users (
    id             BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    email          VARCHAR(254) NOT NULL,
    name           VARCHAR(32)  NOT NULL,
    password_hash  VARCHAR(255) NOT NULL,
    role           VARCHAR(5)   NOT NULL DEFAULT 'USER' CHECK (role IN ('USER', 'ADMIN')),
    locale         VARCHAR(2)   NOT NULL DEFAULT 'pl' CHECK (locale IN ('pl', 'en')),
    weekly_summary BOOLEAN      NOT NULL DEFAULT false,
    created_at     TIMESTAMPTZ  NOT NULL DEFAULT now()
);

CREATE UNIQUE INDEX users_email_lower_key ON users (LOWER(email));
