erDiagram
    USERS {
        bigint id PK
        string name
        string email
        string password
        timestamp email_verified_at
        string remember_token
        timestamp created_at
        timestamp updated_at
    }

    CATEGORIES {
        bigint id PK
        string name
        timestamp created_at
        timestamp updated_at
    }

    WORDS {
        bigint id PK
        bigint category_id FK
        string word
        timestamp created_at
        timestamp updated_at
    }

    HINTS {
        bigint id PK
        bigint word_id FK
        text hint
        timestamp created_at
        timestamp updated_at
    }

    SESSIONS {
        string id PK
        bigint user_id FK
        string ip_address
        text user_agent
        longtext payload
        int last_activity
    }

    CACHE {
        string key PK
        mediumtext value
        int expiration
    }

    JOBS {
        bigint id PK
        string queue
        longtext payload
        int attempts
        int available_at
        int created_at
    }

    CATEGORIES ||--o{ WORDS     : "has many"
    WORDS      ||--o{ HINTS     : "has one"
    USERS      ||--o{ SESSIONS  : "has many"