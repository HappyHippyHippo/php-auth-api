# auth-api

![Code quality](https://github.com/happyhippyhippo/php-auth-api/workflows/Code%20Quality/badge.svg)

### configuration environment variables

#### api-bundle 

![reference](https://github.com/happyhippyhippo/php-api-bundle#configuration-environment-variables)

#### auth-api

##### listing configuration
- HIPPY_LISTING_MAX
  - integer

##### authentication configuration
- HIPPY_AUTH_TRIES
    - integer
- HIPPY_AUTH_COOL_DOWN_TTL
    - string/datetime
- HIPPY_AUTH_CHAP_ENABLED
    - boolean
- HIPPY_AUTH_CHAP_CHALLENGE_TTL
    - string/datetime
- HIPPY_AUTH_LEGACY_ENABLED
    - boolean
- HIPPY_AUTH_SSO_ENABLED
    - boolean
- HIPPY_AUTH_IMPERSONATE_ENABLED
    - boolean
- HIPPY_AUTH_TOKEN_TTL
    - string/datetime
- HIPPY_AUTH_TOKEN_ISSUER
    - string

##### local database configuration
- HIPPY_DATABASE_LOCAL_DRIVER
    - string
- HIPPY_DATABASE_LOCAL_HOST
    - string
- HIPPY_DATABASE_LOCAL_PORT
    - integer
- HIPPY_DATABASE_LOCAL_VERSION
    - string
- HIPPY_DATABASE_LOCAL_USER
    - string
- HIPPY_DATABASE_LOCAL_PASSWORD
    - string
- HIPPY_DATABASE_LOCAL_SCHEMA
    - string
