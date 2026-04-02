#!/usr/bin/env python3
"""Insert consolidated Postman folder at top of APNAFUND_API_COLLECTION.postman_collection.json"""
import json
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
COL = ROOT / "APNAFUND_API_COLLECTION.postman_collection.json"


def auth_json_headers():
    return [
        {"key": "Authorization", "value": "Bearer {{bearer_token}}"},
        {"key": "Accept", "value": "application/json"},
        {"key": "Content-Type", "value": "application/json"},
    ]


def req_post_json(name, url_path, raw_body):
    return {
        "name": name,
        "request": {
            "method": "POST",
            "header": auth_json_headers(),
            "body": {"mode": "raw", "raw": raw_body},
            "url": "{{base_url}}" + url_path,
        },
    }


def req_get(name, url_path):
    return {
        "name": name,
        "request": {
            "method": "GET",
            "header": [{"key": "Accept", "value": "application/json"}],
            "url": "{{base_url}}" + url_path,
        },
    }


def multipart_updates_create():
    return {
        "name": "Updates create (multipart) — campaign_post_updates.php",
        "request": {
            "method": "POST",
            "header": [
                {"key": "Authorization", "value": "Bearer {{bearer_token}}"},
                {"key": "Accept", "value": "application/json"},
            ],
            "body": {
                "mode": "formdata",
                "formdata": [
                    {"key": "op", "value": "create", "type": "text"},
                    {"key": "campaign_id", "value": "{{campaign_id}}", "type": "text"},
                    {"key": "title", "value": "Milestone reached", "type": "text"},
                    {
                        "key": "content",
                        "value": "We reached 50% funding — thank you everyone for supporting this campaign!",
                        "type": "text",
                    },
                    {"key": "is_published", "value": "true", "type": "text"},
                    {"key": "image", "type": "file", "src": []},
                ],
            },
            "url": "{{base_url}}/api/campaign_post_updates.php",
        },
    }


def multipart_updates_update():
    return {
        "name": "Updates update (multipart) — campaign_post_updates.php",
        "request": {
            "method": "POST",
            "header": [
                {"key": "Authorization", "value": "Bearer {{bearer_token}}"},
                {"key": "Accept", "value": "application/json"},
            ],
            "body": {
                "mode": "formdata",
                "formdata": [
                    {"key": "op", "value": "update", "type": "text"},
                    {"key": "campaign_id", "value": "{{campaign_id}}", "type": "text"},
                    {"key": "update_id", "value": "{{update_id}}", "type": "text"},
                    {"key": "title", "value": "Milestone reached", "type": "text"},
                    {
                        "key": "content",
                        "value": "Updated content at least thirty characters long here.",
                        "type": "text",
                    },
                    {"key": "is_published", "value": "true", "type": "text"},
                    {"key": "image", "type": "file", "src": []},
                ],
            },
            "url": "{{base_url}}/api/campaign_post_updates.php",
        },
    }


def build_hub():
    desc = (
        "Ek hi jagah: REST campaign detail, fund lists, story, FAQ, backer updates, aur web (session) par comments. "
        "Protected JSON: Authorization: Bearer {{bearer_token}}. "
        "Web comments: pehle GET /csrf-token, phir browser jaisa Cookie + _token / X-XSRF-TOKEN set karo; "
        "variable {{csrf_token}} (optional) use karo."
    )

    public = {
        "name": "Public — campaign detail & browse",
        "item": [
            req_get(
                "GET /api/campaigns (list)",
                "/api/campaigns?limit=10&offset=0&search=&category=",
            ),
            req_get(
                "GET /api/campaigns/featured",
                "/api/campaigns/featured?limit=5",
            ),
            req_get(
                "GET /api/campaigns/:slug (campaign detail)",
                "/api/campaigns/{{campaign_slug}}",
            ),
        ],
    }

    fund_lists = {
        "name": "Protected — fund lists (creator / donor)",
        "item": [
            {
                "name": "POST fundlist.php — my campaigns",
                "request": {
                    "method": "POST",
                    "header": auth_json_headers(),
                    "body": {
                        "mode": "raw",
                        "raw": '{\n  "status": "Pending"\n}',
                    },
                    "url": "{{base_url}}/api/fundlist.php",
                },
            },
            {
                "name": "POST my_donate_fundlist.php — campaigns I donated to",
                "request": {
                    "method": "POST",
                    "header": auth_json_headers(),
                    "body": {"mode": "raw", "raw": "{}"},
                    "url": "{{base_url}}/api/my_donate_fundlist.php",
                },
            },
        ],
    }

    story = {
        "name": "Protected — story (campaign_story.php)",
        "item": [
            req_post_json(
                "Story read (op=read)",
                "/api/campaign_story.php",
                '{\n  "op": "read",\n  "campaign_id": "{{campaign_id}}"\n}',
            ),
            req_post_json(
                "Story save (op=save)",
                "/api/campaign_story.php",
                '{\n  "op": "save",\n  "campaign_id": "{{campaign_id}}",\n  "description": "Long story text minimum thirty characters required here for save."\n}',
            ),
        ],
    }

    faq = {
        "name": "Protected — FAQ (campaign_faq.php)",
        "item": [
            req_post_json(
                "FAQ list",
                "/api/campaign_faq.php",
                '{\n  "op": "list",\n  "campaign_id": "{{campaign_id}}"\n}',
            ),
            req_post_json(
                "FAQ get",
                "/api/campaign_faq.php",
                '{\n  "op": "get",\n  "campaign_id": "{{campaign_id}}",\n  "faq_id": "{{faq_id}}"\n}',
            ),
            req_post_json(
                "FAQ create",
                "/api/campaign_faq.php",
                '{\n  "op": "create",\n  "campaign_id": "{{campaign_id}}",\n  "question": "When will rewards ship?",\n  "answer": "Within 4 weeks after the campaign ends.",\n  "order": 0\n}',
            ),
            req_post_json(
                "FAQ update",
                "/api/campaign_faq.php",
                '{\n  "op": "update",\n  "campaign_id": "{{campaign_id}}",\n  "faq_id": "{{faq_id}}",\n  "question": "When will rewards ship?",\n  "answer": "Within 6 weeks after the campaign ends.",\n  "order": 1\n}',
            ),
            req_post_json(
                "FAQ delete",
                "/api/campaign_faq.php",
                '{\n  "op": "delete",\n  "campaign_id": "{{campaign_id}}",\n  "faq_id": "{{faq_id}}"\n}',
            ),
        ],
    }

    updates = {
        "name": "Protected — backer updates (campaign_post_updates.php)",
        "item": [
            req_post_json(
                "Updates list (JSON)",
                "/api/campaign_post_updates.php",
                '{\n  "op": "list",\n  "campaign_id": "{{campaign_id}}"\n}',
            ),
            req_post_json(
                "Updates get (JSON)",
                "/api/campaign_post_updates.php",
                '{\n  "op": "get",\n  "campaign_id": "{{campaign_id}}",\n  "update_id": "{{update_id}}"\n}',
            ),
            req_post_json(
                "Updates delete (JSON)",
                "/api/campaign_post_updates.php",
                '{\n  "op": "delete",\n  "campaign_id": "{{campaign_id}}",\n  "update_id": "{{update_id}}"\n}',
            ),
            multipart_updates_create(),
            multipart_updates_update(),
        ],
    }

    web_comments = {
        "name": "Web — comments (session + CSRF, not Bearer)",
        "description": "Laravel web routes. Postman: Cookie header mein laravel_session + XSRF-TOKEN paste karo, ya GET /csrf-token se token lo.",
        "item": [
            req_get("GET /csrf-token", "/csrf-token"),
            {
                "name": "POST campaign comment — /campaign/{slug}/comment",
                "request": {
                    "method": "POST",
                    "header": [
                        {"key": "Accept", "value": "application/json"},
                        {"key": "X-Requested-With", "value": "XMLHttpRequest"},
                        {"key": "Content-Type", "value": "application/x-www-form-urlencoded"},
                    ],
                    "body": {
                        "mode": "urlencoded",
                        "urlencoded": [
                            {"key": "_token", "value": "{{csrf_token}}"},
                            {"key": "title", "value": "Nice campaign"},
                            {"key": "comment", "value": "Supporting this project!"},
                            {"key": "name", "value": "Guest Name"},
                            {"key": "email", "value": "guest@example.com"},
                        ],
                    },
                    "url": "{{base_url}}/campaign/{{campaign_slug}}/comment",
                },
            },
            req_get(
                "GET fetch comments (AJAX) — /campaign/{slug}/fetch-comment",
                "/campaign/{{campaign_slug}}/fetch-comment?skip=1",
            ),
            {
                "name": "POST update page comment — /campaign/{slug}/updates/{updateSlug}/comment",
                "request": {
                    "method": "POST",
                    "header": [
                        {"key": "Accept", "value": "application/json"},
                        {"key": "X-Requested-With", "value": "XMLHttpRequest"},
                        {"key": "Content-Type", "value": "application/x-www-form-urlencoded"},
                    ],
                    "body": {
                        "mode": "urlencoded",
                        "urlencoded": [
                            {"key": "_token", "value": "{{csrf_token}}"},
                            {"key": "comment", "value": "Thanks for this update!"},
                        ],
                    },
                    "url": "{{base_url}}/campaign/{{campaign_slug}}/updates/{{update_slug}}/comment",
                },
            },
        ],
    }

    return {
        "name": "0. Hub — Campaign detail, story, FAQ, updates, comments, fund lists",
        "description": desc,
        "item": [public, fund_lists, story, faq, updates, web_comments],
    }


def main():
    with open(COL, encoding="utf-8") as f:
        data = json.load(f)

    # Remove old hub if re-run
    data["item"] = [x for x in data["item"] if not x.get("name", "").startswith("0. Hub —")]

    hub = build_hub()
    data["item"].insert(0, hub)

    # Variables
    keys = {v["key"] for v in data.get("variable", [])}
    extra = []
    if "update_slug" not in keys:
        extra.append({"key": "update_slug", "value": "your-update-slug"})
    if "csrf_token" not in keys:
        extra.append({"key": "csrf_token", "value": ""})
    data["variable"] = data.get("variable", []) + extra

    with open(COL, "w", encoding="utf-8") as f:
        json.dump(data, f, indent=2, ensure_ascii=False)
        f.write("\n")

    print("OK:", COL)


if __name__ == "__main__":
    main()
