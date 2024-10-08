# Authentication

---

- 



To authenticate requests to the Swear-Stop API, you must include an API key in the request headers. You can obtain your API key by visiting the [Swear-Stop Profile Page](https://swear-stop.com/profile).

![img.png](/images/docs_img_1.png)

### How to Use the API Key

Once you have obtained your API key, it should be included in all API requests as a **Bearer Token** in the request headers.

### Example Header with API Key

```http
Authorization: Bearer YOUR_API_KEY
```
### Example Request with Authentication

Here is an example of how to authenticate a request to the text filtering endpoint:

```bash
curl -X POST https://swear-stop.com/api/v1/text-filter \
-H "Authorization: Bearer YOUR_API_KEY" \
-H "Content-Type: application/json" \
-d '{
    "sentence": "This is a sample text with profane content.",
    "moderation_categories": ["*"]
}'
```

Replace `YOUR_API_KEY` with the actual key from your profile.

### Important Notes

> {warning} 
- Always keep your API key secure and do not share it publicly. 
- If your API key is compromised, you can regenerate it by visiting the [Swear-Stop Profile Page](https://swear-stop.com/profile).
- The API key must be included with every request to access the API.

