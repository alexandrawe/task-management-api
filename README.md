# task-management-api

# API Docs

All responses are returned in standard JSON format. Every request must include a `Content-Type: application/json` header, a valid JSON body, and authentication via a valid Bearer token - except for the login endpoint - in the `Authorization` header.

### Overview

##### /api/login
* `POST` : [Log user in with email and password and return token](#log-user-in-with-email-and-password-and-return-token)

##### /api/tasks
* `GET` : [Get tasks](#get-tasks)
* `POST` : [Store new task](#store-new-task)

##### /api/tasks/overdue
* `GET` : [Get all overdue tasks](#get-all-overdue-tasks)

##### /api/tasks/:id
* `GET` : [Get task](#get-task)
* `PATCH` : [Update task](#update-task)
* `DELETE` : [Delete task](#delete-task)

##### /api/projects/:id/tasks
* `GET` : [Get all tasks for project](#get-all-tasks-for-project)

##### /api/users/:id/tasks
* `GET` : [Get all tasks for user](#get-all-tasks-for-user)

___

### Log user in with email and password and return token
`POST` /api/login

`Accept: application/json`

| Body params |                                                     |
| ----------- | --------------------------------------------------- |
| email       | email, required                                     |
| password    | string, required                                    |

##### Responses

`200 OK`
``` json
{
  "token": "10|TASKxyz...."
}
```

`401 Unauthorized`
``` json
{
  "message": "Wrong credentials."
}
```

`422 Unprocessable Content`
``` json
{
  "message": "The email field is required. (and 1 more error)",
  "errors": {
    "email": [
      "The email field is required."
    ],
    "password": [
      "The password field is required."
    ]
  }
}
```

### Get tasks
`GET` /api/tasks

Returns a paginated list of tasks, with 20 task per page.

##### Responses

`200 OK`
``` json
{
  "tasks": {
    "current_page": 1,
    "data": [
      {
        "id": 2,
        "title": "Temporibus totam non minus est similique dolorem similique.",
        "description": "Magni ut non sit fugiat error dicta est ...",
        "deadline": "2025-05-12 12:12:00",
        "user_id": 1,
        "state": "in_progress",
        "created_by": 1,
        "project_id": 2,
        "created_at": "2025-04-24T17:00:31.000000Z",
        "updated_at": "2025-04-24T17:00:31.000000Z"
      },
      // ...
    ],
    "first_page_url": "http://127.0.0.1:8000/api/tasks?page=1",
    "from": 1,
    "last_page": 2,
    "last_page_url": "http://127.0.0.1:8000/api/tasks?page=2",
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "active": false
      },
      {
        "url": "http://127.0.0.1:8000/api/tasks?page=1",
        "label": "1",
        "active": true
      },
      {
        "url": "http://127.0.0.1:8000/api/tasks?page=2",
        "label": "2",
        "active": false
      },
      {
        "url": "http://127.0.0.1:8000/api/tasks?page=2",
        "label": "Next &raquo;",
        "active": false
      }
    ],
    "next_page_url": "http://127.0.0.1:8000/api/tasks?page=2",
    "path": "http://127.0.0.1:8000/api/tasks",
    "per_page": 20,
    "prev_page_url": null,
    "to": 20,
    "total": 32
  }
}
```

`401 Unauthorized`
``` json
{
  "error": "Unauthorized"
}
```

### Store new task
`POST` /api/tasks

| Body params |                                                     |
| ----------- | --------------------------------------------------- |
| title       | string, required, max 255 characters                |
| description | string, required                                    |
| deadline    | date, required, can only be a date in the future    |
| user_id     | number, id of user to assign the task to            |
| project_id  | number, id of project the task belongs to           |

##### Responses
`200 OK`
``` json
{
  "task": {
    "title": "New Task",
    "description": "Short description",
    "deadline": "2025-05-14 18:00:00",
    "user_id": "3",
    "created_by": 3,
    "project_id": "1",
    "updated_at": "2025-05-12T11:54:48.000000Z",
    "created_at": "2025-05-12T11:54:48.000000Z",
    "id": 39
  },
  "message": "Task was successfully created."
}
```

`401 Unauthorized`
``` json
{
  "error": "Unauthorized"
}
```

`422 Unprocessable Content`
``` json
{
  "message": "The title field is required. (and 1 more error)",
  "errors": {
    "title": [
      "The title field is required."
    ],
    "description": [
      "The description field is required."
    ]
  }
}
```

### Get all overdue tasks
`GET` /api/tasks/overdue

Returns a paginated list of all open tasks where deadline is in past with 20 task per page.

##### Responses
`200 OK`
``` json
{
  "tasks": {
    "current_page": 1,
    "data": [
          {
            "id": 4,
            "title": "Nobis et ipsa molestiae et quis delectus et.",
            "description": "Facilis quidem error quis sint ...",
            "deadline": "2025-05-11 16:00:00",
            "user_id": 1,
            "state": "in_progress",
            "created_by": 1,
            "project_id": 2,
            "created_at": "2025-04-24T17:00:31.000000Z",
            "updated_at": "2025-04-24T17:00:31.000000Z"
          },
          // ...
        ],
    "first_page_url": "http://127.0.0.1:8000/api/tasks/overdue?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://127.0.0.1:8000/api/tasks/overdue?page=1",
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "active": false
      },
      {
        "url": "http://127.0.0.1:8000/api/tasks/overdue?page=1",
        "label": "1",
        "active": true
      },
      {
        "url": null,
        "label": "Next &raquo;",
        "active": false
      }
    ],
    "next_page_url": null,
    "path": "http://127.0.0.1:8000/api/tasks/overdue",
    "per_page": 20,
    "prev_page_url": null,
    "to": 7,
    "total": 7
  }
}
```

`401 Unauthorized`
``` json
{
  "error": "Unauthorized"
}
```

### Get task
`GET` /api/tasks/:id

##### Responses
`200 OK`
``` json
{
  "task": {
    "id": 15,
    "title": "Lorem ipsum",
    "description": "Little description",
    "deadline": "2025-06-03 10:00:00",
    "user_id": null,
    "state": "todo",
    "created_by": 1,
    "project_id": 5,
    "created_at": "2025-04-26T11:38:00.000000Z",
    "updated_at": "2025-04-26T11:38:00.000000Z"
  }
}
```

`401 Unauthorized`
``` json
{
  "error": "Unauthorized"
}
```

### Update task
`PATCH` /api/tasks/:id

`Accept: application/json`

| Path params |                                                     |
| ----------- | --------------------------------------------------- |
| id	        | number, required, id of task                        |

| Body params |                                                     |
| ----------- | --------------------------------------------------- |
| title       | string, required, max 255 characters                |
| description | string                                              |
| deadline    | date, required, can only be a date in the future    |
| user_id     | number, id of user to assign the task to            |
| state       | string, can only be "todo", "in_progress" or "done" |
| project_id  | number, id of project the task belongs to           |

##### Responses
`200 OK`
``` json
{
  "task": {
    "id": 35,
    "title": "Vel atque nihil",
    "description": "description",
    "deadline": "2025-05-12 12:12:04",
    "user_id": 1,
    "state": "done",
    "created_by": 3,
    "project_id": 1,
    "created_at": "2025-05-10T09:56:03.000000Z",
    "updated_at": "2025-05-12T12:02:37.000000Z"
  },
  "message": "Task was successfully updated."
}
```

`401 Unauthorized`
``` json
{
  "error": "Unauthorized"
}
```

`422 Unprocessable Content`
``` json
{
  "message": "The title field is required. (and 1 more error)",
  "errors": {
    "title": [
      "The title field is required."
    ],
    "description": [
      "The description field is required."
    ]
  }
}
```

### Delete task
`DELETE` /api/tasks/:id

| Path params |                                                     |
| ----------- | --------------------------------------------------- |
| id	        | number, required, id of task                        |

##### Responses
`200 OK`
``` json
{
  "task_id": "1",
  "message": "Task was successfully deleted."
}
```

`401 Unauthorized`
``` json
{
  "error": "Unauthorized"
}
```

### Get all tasks for project
`GET` /api/projects/:id/tasks

Returns the project and belonging task. Tasks are pagianted with 20 tasks per page.

| Path params |                                                     |
| ----------- | --------------------------------------------------- |
| id	        | number, required, id of project                     |

##### Responses
`200 OK`
``` json
{
    "project": {
        "id": 1,
        "name": "Sapiente dolorem fuga aut.",
        "created_at": "2025-05-05T12:55:20.000000Z",
        "updated_at": "2025-05-05T12:55:20.000000Z"
    },
  "tasks": {
    "current_page": 1,
    "data": [
            {
                "id": 13,
                "title": "test title",
                "description": "description",
                "deadline": "2026-05-11 00:00:00",
                "user_id": 1,
                "state": "in_progress",
                "created_by": 1,
                "project_id": 2,
                "created_at": "2025-04-26T11:20:08.000000Z",
                "updated_at": "2025-05-11T11:23:23.000000Z"
            }
            // ...
        ],
    "first_page_url": "http://127.0.0.1:8000/api/project/1/tasks?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://127.0.0.1:8000/api/project/1/tasks?page=1",
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "active": false
      },
      {
        "url": "http://127.0.0.1:8000/api/project/1/tasks?page=1",
        "label": "1",
        "active": true
      },
      {
        "url": null,
        "label": "Next &raquo;",
        "active": false
      }
    ],
    "next_page_url": null,
    "path": "http://127.0.0.1:8000/api/project/1/tasks",
    "per_page": 20,
    "prev_page_url": null,
    "to": 8,
    "total": 8
  }
}
```

`401 Unauthorized`
``` json
{
  "error": "Unauthorized"
}
```

`404 Not Found`
``` json
{
  "message": "Project not found."
}
```

### Get all tasks for user
`GET` /api/users/:id/tasks

Returns the user and belonging task. Tasks are pagianted with 20 tasks per page.

| Path params |                                                     |
| ----------- | --------------------------------------------------- |
| id	        | number, required, id of user                        |

##### Responses
`200 OK`
``` json
{
  "user": {
    "id": 1,
    "name": "Adriana Harris",
    "email": "wframi@example.com",
    "email_verified_at": "2025-04-24T16:52:54.000000Z",
    "created_at": "2025-04-24T16:52:54.000000Z",
    "updated_at": "2025-04-24T16:52:54.000000Z"
  },
  "tasks": {
    "current_page": 1,
    "data": [
            {
                "id": 13,
                "title": "Test task",
                "description": "description",
                "deadline": "2026-05-11 00:00:00",
                "user_id": 1,
                "state": "in_progress",
                "created_by": 1,
                "project_id": 2,
                "created_at": "2025-04-26T11:20:08.000000Z",
                "updated_at": "2025-05-11T11:23:23.000000Z"
            },
            {
                "id": 32,
                "title": "Test test 123",
                "description": "Short description",
                "deadline": "2025-05-12 12:12:04",
                "user_id": 1,
                "state": "todo",
                "created_by": 3,
                "project_id": 1,
                "created_at": "2025-05-10T09:45:10.000000Z",
                "updated_at": "2025-05-10T09:45:10.000000Z"
            },
            // ...
        ]
    "first_page_url": "http://127.0.0.1:8000/api/users/1/tasks?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://127.0.0.1:8000/api/users/1/tasks?page=1",
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "active": false
      },
      {
        "url": "http://127.0.0.1:8000/api/users/1/tasks?page=1",
        "label": "1",
        "active": true
      },
      {
        "url": null,
        "label": "Next &raquo;",
        "active": false
      }
    ],
    "next_page_url": null,
    "path": "http://127.0.0.1:8000/api/users/1/tasks",
    "per_page": 20,
    "prev_page_url": null,
    "to": 7,
    "total": 7
  }
}
```

`401 Unauthorized`
``` json
{
  "error": "Unauthorized"
}
```

`404 Not Found`
``` json
{
  "message": "User not found."
}
```