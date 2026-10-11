#!/usr/bin/env python3
import sys, json, yaml, requests, urllib3
urllib3.disable_warnings()

yaml_path = sys.argv[1]
target_id = sys.argv[2]
access_token = sys.argv[3]

class Loader(yaml.SafeLoader):
    pass

def unsafe_constructor(loader, node):
    return loader.construct_scalar(node)

Loader.add_constructor('!unsafe', unsafe_constructor)

with open(yaml_path) as f:
    data = yaml.load(f, Loader=Loader)

meta = data.get("metadata", {})

body = {
    "target_id": str(target_id),
    "scenario": meta.get("scenario", ""),
    "solution": meta.get("solution", ""),
    "pre_exploitation": meta.get("pre_exploitation", ""),
    "post_exploitation": meta.get("post_exploitation", ""),
    "pre_credits": meta.get("pre_credits", ""),
    "post_credits": meta.get("post_credits", ""),
    "instructions": meta.get("instructions", ""),
}

url = f"https://mui.hackmex.mx/index.php?r=restapi/target-metadata/create&access-token={access_token}"
r = requests.post(url, json=body, verify=False)

print("STATUS:", r.status_code)
print("HEADERS:")
for k, v in r.headers.items():
    print(f"  {k}: {v}")
print("BODY:")
print(r.text)
