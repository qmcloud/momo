## Environment variables

Roomler application is configured via the following set of environment variables used in `/config/index.js`.

For the sake of easier understanding, we will group them by the function they perform.

### Application (REQUIRED)
- **URL** - Public URL of the UI (frontend) application
- **API_URL** - Public URL of the API (backend) application. In production this value is the same as **URL**, but in development they differ e.g. `URL=http://localhost:3000` and `API_URL=http://localhost:3001`

### Database (REQUIRED)
- **DB_CONN** - Mongo DB connection string

**IMPORTANT**: See [details](./deps-mongo.md)

### Web Sockets (OPTIONAL)
- **WS_SCALEOUT_ENABLED** - in case it is set to `true`, then your redis service is required
- **WS_SCALEOUT_HOST** - redis host name (must be in the same docker network as roomler `backend`)

**IMPORTANT**: Web Socket scaleout is implemented via Redis PUB/SUB mechanism. So if we start the app in development environment, without `pm2`, these variables are optional, otherwise you need to enable the scaleout and provide the `redis` hostname as well as make sure your redis microservice is attached in the `backend` docker network (see [this](./deps-redis.md))

### Email sending (REQUIRED, one of these three options)
- **SENDGRID_API_KEY** - For sending Emails via your Sendgrid API key
- **GMAIL_USER** & **GMAIL_PASSWORD** - For sending eails via your GMAIL account
- **SMTP_HOST** & **SMTP_PORT** & **SMTP_SECURE** & **SMTP_USER** & **SMTP_PASSWORD** - For sending emails via your own SMTP server

- **FROM_EMAIL** - Email address from which all emails are being sent

**IMPORTANT**: You need to select one of the three options for sending emails, otherwise creating an account will throw an error.

### Authentication (OAuth) (OPTIONAL)
- **FACEBOOK_ID** - OAuth Facebook ID
- **FACEBOOK_SECRET** -  - OAuth Facebook Secret
- **GOOGLE_ID** - OAuth Google ID
- **GOOGLE_SECRET** - OAuth Google Secret
- **GITHUB_ID** -  OAuth Github ID
- **GITHUB_SECRET** - OAuth Github Secret
- **LINKEDIN_ID** - OAuth LinkedIn ID
- **LINKEDIN_SECRET** - OAuth LinkedIn Secret

**IMPORTANT**: If you don't provide OAUTH (Facebook, Google, LinkedIn, Github) ID/SECRET envs, you will still be able to have local registration (username/email/password), but clicking on the OAUTH buttons in the `/-/auth/login` or `/-/auth/register` routes will throw an error.

### Video Conferencing (REQUIRED)
- **JANUS_URL** - Your Janus server public URL e.g. `wss://janus.yourdomain.com/janus_ws`
- **TURN_URL**  - Your Coturn server public URL e.g. `wss://coturn.yourdomain.com`
- **TURN_USERNAME** - Coturn Username
- **TURN_PASSWORD** - Coturn Pasword

**IMPORTANT**: Janus & Coturn are required micro service dependencies need to Room creation, video conferencing & reliable connects in NAT scenarios. See [Janus](./deps-janus.md) & [Coturn](./deps-coturn.md) for more details

### Chat (OPTIONAL)
- **GIPHY_API_KEY** - Your Giphy API key

**IMPORTANT**: If you don't provide Giphy API KEY, adding giphys will throw an error. TODO - preven hide the giphy button if no Giphy API key is provided.

### Admin (OPTIONAL)
- **SUPER_ADMIN_EMAILS** - Email of the Roomler Super admin, that can look in to the analyitcs of user live visits and page stats
- **GOOGLE_ANALYTICS_ID**  - Your google anaytics Id.

**IMPORTANT**: If `SUPER_ADMIN_EMAILS` is not provided, you won't be able to access the analytics routes `/admin/stats`. Regarding `GOOGLE_ANALYTICS_ID`, in `development`, this env variable is being set before running the `npm run dev:api & npm run dev:ui`. In `production `is actually needed during the `nuxt build` (app compilation), hence you can put it a separate `.arg` file, similar to `.env` with the following content:
```
GOOGLE_ANALYTICS_ID=YOUR_GOOGLE_ANALYICS
```
and then use the `build.sh` script, which will inject it during `docker build` instead of `docker run`.


### Push notifications (OPTIONAL)
- **WEB_PUSH_CONTACT** - e.g. "mailto: your_contact_email@gmail.com"
- **WEB_PUSH_PUBLISH_KEY** - Your VAPID public key
- **WEB_PUSH_PRIVATE_KEY** - Your VAPID private key

**IMPORTANT**: You can generate your VAPID keys (for push notifications):
`./node_modules/.bin/web-push generate-vapid-keys`

### SIP via Asterisk (OPTIONAL, EXPERIMENTAL FEATURE)
- **ASTERISK_URL** - URL of your Asterisk server
- **ASTERISK_ARI_URL** - URL of ARI endpoint of your Asterisk server
- **ASTERISK_ARI_USERNAME** - Username of Asterisk ARI
- **ASTERISK_ARI_PASSWORD** - Password of Asterisk ARI
- **ASTERISK_ARI_APP** - Application name that handles the ARI events
- **ASTERISK_ARI_GENERATE_ACCOUNTS** - `true` if during Roomler start up, for each `user` in `Roomler DB` we want to create/update corresponding `endpoint`, `aor` and `auth` records in Asterisk, whereat `username=sip:${user._id}@${ASTERISK_URL}` and `password=${user.createdAt.getTime()}`

**IMPORTANT**: This is an experimental feature and it's not recommended without advanced understanding of SIP and Asterisk.

How it is supposed to work? During Room creation, in the Media section, if we select a option `Use SIP bridge`, then conferences held in this room will SFU all video streams via the ` Janus VideoRoom plugin` and mix all audio streams via `Janus Sip Plugin` to the dynamically created Asterisk mixed bridge `${room._id}`. Each user is accessing Asterisk via the its own endpoint: `username=sip:${user._id}@${ASTERISK_URL}` and `password=${user.createdAt.getTime()}`