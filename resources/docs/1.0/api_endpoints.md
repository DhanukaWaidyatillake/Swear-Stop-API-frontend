# API Usage

---

- 

The **Profanity Filtering API** allows you to filter out profanity or objectionable content from a given text based on several moderation categories. Users can specify which categories to filter, or apply all available categories.

### Endpoint
`POST https://swear-stop.com/api/v1/text-filter`

### Request Body
```json
{
  "sentence": "Sentence for filtering",
  "moderation_categories": [
    "*"
  ]
}
```

### Parameters

| Parameter               | Type     | Description                                                                                                                                         |
|-------------------------|----------|-----------------------------------------------------------------------------------------------------------------------------------------------------|
| `sentence`              | `string` | The text string that you want to filter for profanity.                                                                                               |
| `moderation_categories` | `array`  | A list of moderation categories to filter by. You can specify one or more categories from the list below. If `"*"` is used, all categories will be applied. |

### Moderation Categories:

- **alcohol**: Filters references to alcohol.
- **gambling**: Filters content related to gambling.
- **political_and_religious**: Filters political and religious content that might be considered offensive.
- **recreational_drugs**: Filters references to drugs and related terms.
- **sexual**: Filters sexual content and profanity.
- **slang**: Filters slang words that could be considered offensive.
- **violence**: Filters terms related to violence.
- **weapons_and_ammunition**: Filters references to weapons and ammunition.

### Example Request

```json
{
    "sentence": "The party was lit, but John brought a gun and it went south!",
    "moderation_categories": [
        "violence", "weapons_and_ammunition"
    ]
}
```

### Example Response

The API will return the filtered text with any detected profanities replaced or flagged.

```json
{
    "status": "success",
    "profanity": {
        "words": {
            "weapons_and_ammunition": [
                {
                    "flagged_word": "gun",
                    "sentence_token": "gun"
                }
            ],
            "blacklisted_words": []
        },
        "phrases": []
    },
    "whitelisted_words_in_text": [],
    "grawlix": [],
    "timestamp": 1728384195
}
```
