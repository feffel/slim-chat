# slim-chat

## Setup
```
make build && make up
make install && make migrate
```
Server will run at `localhost:80`


## Endpoints

|  Fn  | Method | Route | Payload|
| -- | -- | -- | -- |
| Register a new user | POST  | `api/users/register` | *body*`{"username": "felfel"}` |
| List conversations  | GET | `api/conversations` | *query* *optional* `?include=messages`  |
| Get conversation | GET | `api/conversations/{id}` | *query* *optional* `?include=messages` |
| Find conversation by user | GET | `api/conversations/search` |*query*  `?user={user_id}` *optional* `?include=messages` |
| List messages in a conversation  | GET | `api/conversations/{id}/messages` |  |
| Get message | GET | `api/conversations/{conversation_id}/messages/{id}` | |
| Send message | POST | `api/conversations/{conversation_id}/messages` | *body*`{"content": "hello world"}`  |


## Components


### Auth

Super simple auth using user id
```
-H 'Authorization:{user_id}'
```

### Pagination

Cursor pagination available on list endpoints

|  Parameter  | Description |
| -- | -- |
| `?cursor={int}` | Fetch data starting from this pointer |
| `?limit={int}`  | Max count of data to be fetched |
| `?prev={int}` | Last used cursor, will be returned in response |


### Serialization

Customize response data per request

|  Parameter  | Description |
| -- | -- |
| `?include={field1},{field2}` | Get default response plus the requested fields |
| `?exclude={field1},{field2}`  | Remove requested fields from the default response |


