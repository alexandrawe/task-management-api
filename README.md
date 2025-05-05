# task-management-api

# API Docs
### Get tasks <span style="background:#0e9b71;color:white;padding:.4rem;border-radius:16px;font-size:12px">GET</span>
/api/tasks
##### Example Response
``` json
{
	"tasks": [
		{
			"id": 1,
			"title": "Rem et est vel natus",
			"description": "Et doloribus consequatur...",
			"state": "todo",
			"created_by": 1,
			"created_at": "2025-04-24T17:00:19.000000Z",
			"updated_at": "2025-04-24T17:00:19.000000Z"
		},
		{
			"id": 2,
			"title": "Temporibus totam",
			"description": "Magni ut non sit fugiat...",
			"state": "in_progress",
			"created_by": 1,
			"created_at": "2025-04-24T17:00:31.000000Z",
			"updated_at": "2025-04-24T17:00:31.000000Z"
		}
	]
}
```

### Get task <span style="background:#0e9b71;color:white;padding:.4rem;border-radius:16px;font-size:12px">GET</span>
/api/tasks/{task_id}
##### Example Response
``` json
{
	"task": {
		"id": 3,
		"title": "Explicabo nostrum quaerat accusantium.",
		"description": "Pariatur et praesentium...",
		"state": "in_progress",
		"created_by": 1,
		"created_at": "2025-04-24T17:00:31.000000Z",
		"updated_at": "2025-04-24T17:00:31.000000Z"
	}
}
```

### Create task <span style="background:#0171c2;color:white;padding:.4rem;border-radius:16px;font-size:12px">POST</span>
/api/tasks

##### Example Request
``` json
{
	"title": "Test task",
	"description": "Short description"
}
```
##### Example Response
``` json
{
	"task": {
		"title": "Test task",
		"description": "Short description",
		"created_by": 1,
		"updated_at": "2025-04-26T12:09:56.000000Z",
		"created_at": "2025-04-26T12:09:56.000000Z",
		"id": 17
	},
	"message": "Task was successfully created."
}
```
### Edit task <span style="background:#df7d03;color:white;padding:.4rem;border-radius:16px;font-size:12px">PATCH</span>
/api/tasks/{task_id}

| Body params |                                                     |
| ----------- | --------------------------------------------------- |
| title       | string, required, max 255 characters                |
| description | string                                              |
| state       | string, can only be "todo", "in_progress" or "done" |
##### Example Request
``` json
{
	"title": "New title",
	"description": "description",
	"state": "in_progress"
}
```
##### Example Response
``` json
{
	"task": {
		"id": 13,
		"title": "New title",
		"description": "description",
		"state": "in_progress",
		"created_by": 1,
		"created_at": "2025-04-26T11:20:08.000000Z",
		"updated_at": "2025-04-26T12:23:57.000000Z"
	},
	"message": "Task was successfully updated."
}
```
### Delete task <span style="background:#c71b29;color:white;padding:.4rem;border-radius:16px;font-size:12px">DELETE</span>
/api/tasks/{task_id}
##### Example Response
``` json
{
	"task_id": "19",
	"message": "Task was successfully deleted."
}
```