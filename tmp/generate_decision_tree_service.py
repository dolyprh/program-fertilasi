#!/usr/bin/env python3
"""Generate DecisionTreeService.php and CurrentActionSeeder items from RapidMiner tree export."""

from __future__ import annotations

import re
from collections import OrderedDict
from pathlib import Path

TRAINING_COUNT = 597
TREE_PATH = Path(r"d:\ME\JOKI\TA Bambang\program-fertilasi\tmp\tree_export.txt")
SERVICE_PATH = Path(r"d:\ME\JOKI\TA Bambang\program-fertilasi\app\Services\DecisionTreeService.php")
SEEDER_PATH = Path(r"d:\ME\JOKI\TA Bambang\program-fertilasi\database\seeders\CurrentActionSeeder.php")

PROMIL_CODES = {
    "Alami": "ALAMI",
    "Belum Promil": "BELUM_PROMIL",
    "IUI": "IUI",
    "IVF": "IVF",
    "Pemeriksaan Dasar": "PEMERIKSAAN_DASAR",
    "Senggama Terjadwal": "SENGGAMA_TERJADWAL",
}

# Preserve existing codes where possible
KNOWN_ACTION_CODES = {
    "HO": "HO",
    "Kontrol D2-D5": "KONTROL_D2_D5",
    "PRP Ovarium Transvaginal": "PRP_OVARIUM_TRANSVAGINAL",
    "Paket Akupuntur 12x Ke 6": "PAKET_AKUPUNTUR_12X_KE_6",
    "Kontrol USG": "KONTROL_USG",
    "Kontrol Usg": "KONTROL_USG",
}


def to_action_code(name: str) -> str:
    if name in KNOWN_ACTION_CODES:
        return KNOWN_ACTION_CODES[name]
    code = name.upper()
    code = code.replace("+", " PLUS ")
    code = code.replace("/", " ")
    code = code.replace("-", "_")
    code = code.replace("(", " ")
    code = code.replace(")", " ")
    code = re.sub(r"[^A-Z0-9]+", "_", code)
    code = re.sub(r"_+", "_", code).strip("_")
    return code


def parse_dist(raw: str) -> dict[str, int]:
    dist = {}
    for part in raw.split(","):
        part = part.strip()
        if not part:
            continue
        k, v = part.split("=")
        dist[k.strip()] = int(v.strip())
    return dist


def leaf_stats(dist: dict[str, int], prediction: str) -> tuple[int, int, float, float]:
    support = sum(dist.values())
    majority = dist.get(prediction, 0)
    confidence = round(majority / support * 100, 2) if support else 0.0
    support_pct = round(support / TRAINING_COUNT * 100, 2) if support else 0.0
    return support, majority, confidence, support_pct


def depth(line: str) -> int:
    # Count leading "|   " groups; root lines have depth 0
    m = re.match(r"^((?:\|   )*)", line)
    if not m:
        return 0
    return len(m.group(1)) // 4


def parse_tree(text: str) -> list[dict]:
    """Return list of leaf rule dicts with condition stack."""
    leaves: list[dict] = []
    stack: list[tuple[int, str]] = []  # (depth, condition text)

    leaf_re = re.compile(
        r"^(?P<head>.+?):\s*(?P<pred>PRO .+?)\s*\{(?P<dist>[^}]+)\}\s*$"
    )
    branch_re = re.compile(r"^(?P<head>.+?)\s*$")

    for raw in text.splitlines():
        line = raw.rstrip()
        if not line.strip():
            continue
        d = depth(line)
        content = re.sub(r"^(?:\|   )*", "", line).strip()
        # normalize unicode ≤
        content = content.replace("≤", "<=").replace("–", "-")

        # pop stack to parent depth
        while stack and stack[-1][0] >= d:
            stack.pop()

        m = leaf_re.match(content)
        if m:
            head = m.group("head").strip()
            pred = m.group("pred").strip()
            dist = parse_dist(m.group("dist"))
            conditions = [c for _, c in stack] + [head]
            support, majority, conf, pct = leaf_stats(dist, pred)
            leaves.append(
                {
                    "conditions_raw": conditions,
                    "prediction": pred,
                    "dist": dist,
                    "support": support,
                    "majority": majority,
                    "confidence": conf,
                    "support_pct": pct,
                }
            )
            continue

        # non-leaf branch
        head = content.rstrip(":")
        stack.append((d, head))

    return leaves


def normalize_conditions(conds: list[str]) -> dict:
    """Parse conditions into structured fields."""
    promil = None
    age_ops: list[tuple[str, float]] = []
    action_name = None

    for c in conds:
        if c.startswith("Promil Saat Ini = "):
            promil = PROMIL_CODES[c.split(" = ", 1)[1]]
        elif c.startswith("Umur > "):
            age_ops.append((">", float(c.split(" > ", 1)[1])))
        elif c.startswith("Umur <= "):
            age_ops.append(("<=", float(c.split(" <= ", 1)[1])))
        elif c.startswith("Tindakan Hari Ini = "):
            action_name = c.split(" = ", 1)[1]
        else:
            raise ValueError(f"Unknown condition: {c}")

    return {
        "promil": promil,
        "age_ops": age_ops,
        "action_name": action_name,
        "action_code": to_action_code(action_name) if action_name else None,
    }


def age_path_labels(age_ops: list[tuple[str, float]]) -> list[str]:
    labels = []
    for op, val in age_ops:
        pretty = str(val).replace(".", ",")
        if pretty.endswith(",0"):
            pretty = pretty[:-2]
        elif pretty.endswith("0") and "," in pretty:
            pretty = pretty.rstrip("0").rstrip(",")
        labels.append(f"Umur {op} {pretty}")
    return labels


def human_conditions(raw_conds: list[str]) -> list[str]:
    out: list[str] = []
    for c in raw_conds:
        if c.startswith("Promil Saat Ini = "):
            out.append(f"Promil Saat Ini = {PROMIL_CODES[c.split(' = ', 1)[1]]}")
        elif c.startswith("Umur > "):
            out.append(f"Umur > {fmt_thresh(float(c.split(' > ', 1)[1]))}")
        elif c.startswith("Umur <= "):
            out.append(f"Umur <= {fmt_thresh(float(c.split(' <= ', 1)[1]))}")
        elif c.startswith("Tindakan Hari Ini = "):
            name = c.split(" = ", 1)[1]
            out.append(f"Tindakan Hari Ini = {to_action_code(name)}")
        else:
            raise ValueError(c)
    return out


def php_escape(s: str) -> str:
    return s.replace("\\", "\\\\").replace("'", "\\'")


def generate_service(leaves: list[dict]) -> str:
    # enrich
    for i, leaf in enumerate(leaves, start=1):
        norm = normalize_conditions(leaf["conditions_raw"])
        leaf["norm"] = norm
        leaf["code"] = f"R{i}"
        leaf["conditions"] = human_conditions(leaf["conditions_raw"])

    # Collect action names for seeder (unique by code, prefer longer/canonical name)
    actions: OrderedDict[str, str] = OrderedDict()
    for leaf in leaves:
        name = leaf["norm"]["action_name"]
        if not name:
            continue
        code = leaf["norm"]["action_code"]
        if code not in actions:
            actions[code] = name
        else:
            # prefer non-all-caps USG variant display
            if name == "Kontrol Usg":
                actions[code] = name

    # Build rules() PHP array
    rules_php = []
    for leaf in leaves:
        conds = ",\n                    ".join(
            f"'{php_escape(c)}'" for c in leaf["conditions"]
        )
        rules_php.append(
            f"""            [
                'code' => '{leaf['code']}',
                'conditions' => [
                    {conds},
                ],
                'prediction' => '{php_escape(leaf['prediction'])}',
                'confidence' => {leaf['confidence']:.2f},
                'support_count' => {leaf['support']},
                'support_percentage' => {leaf['support_pct']:.2f},
            ]"""
        )

    # Group by promil for classify
    by_promil: dict[str, list[dict]] = OrderedDict()
    for leaf in leaves:
        by_promil.setdefault(leaf["norm"]["promil"], []).append(leaf)

    classify_methods = []
    match_arms = []

    for promil, items in by_promil.items():
        method = f"classify{''.join(p.capitalize() for p in promil.lower().split('_'))}"
        # Fix method names
        method_map = {
            "ALAMI": "classifyAlami",
            "BELUM_PROMIL": "classifyBelumPromil",
            "IUI": "classifyIui",
            "IVF": "classifyIvf",
            "PEMERIKSAAN_DASAR": "classifyPemeriksaanDasar",
            "SENGGAMA_TERJADWAL": "classifySenggamaTerjadwal",
        }
        method = method_map[promil]
        needs_action = any(i["norm"]["action_code"] for i in items)
        match_arms.append(
            f"            '{promil}' => $this->{method}("
            + ("$age, $action" if needs_action else "$age")
            + "),"
        )
        classify_methods.append(generate_classify_method(method, promil, items, needs_action))

    match_arms_str = "\n".join(match_arms)

    return f"""<?php

namespace App\\Services;

class DecisionTreeService
{{
    private const int TRAINING_COUNT = {TRAINING_COUNT};

    /**
     * Apply C4.5 decision rules from RapidMiner to normalized attributes.
     *
     * @param  array{{age: int, current_promil_code: string, current_action_code: string}}  $attributes
     * @return array{{
     *     matched: bool,
     *     prediction: string|null,
     *     rule_code: string|null,
     *     decision_path: list<string>,
     *     confidence: float|null,
     *     support_count: int|null,
     *     support_percentage: float|null
     * }}
     */
    public function classify(array $attributes): array
    {{
        $age = $attributes['age'];
        $promil = $attributes['current_promil_code'];
        $action = $attributes['current_action_code'];

        return match ($promil) {{
{match_arms_str}
            default => $this->unmatched([
                \"Promil Saat Ini = {{$promil}}\",
                'Tidak ditemukan decision rule yang sesuai',
            ]),
        }};
    }}

    /**
     * @return list<array{{
     *     code: string,
     *     conditions: list<string>,
     *     prediction: string,
     *     confidence: float,
     *     support_count: int,
     *     support_percentage: float
     * }}>
     */
    public function rules(): array
    {{
        return [
{',\n'.join(rules_php)},
        ];
    }}

{chr(10).join(classify_methods)}
    /**
     * @param  list<string>  $decisionPath
     * @return array{{
     *     matched: bool,
     *     prediction: string|null,
     *     rule_code: string|null,
     *     decision_path: list<string>,
     *     confidence: float|null,
     *     support_count: int|null,
     *     support_percentage: float|null
     * }}
     */
    private function leaf(
        string $prediction,
        string $ruleCode,
        array $decisionPath,
        int $majorityCount,
        int $supportCount,
    ): array {{
        return $this->matched(
            prediction: $prediction,
            ruleCode: $ruleCode,
            decisionPath: [...$decisionPath, \"Promil Selanjutnya = {{$prediction}}\"],
            confidence: round(($majorityCount / $supportCount) * 100, 2),
            supportCount: $supportCount,
            supportPercentage: round(($supportCount / self::TRAINING_COUNT) * 100, 2),
        );
    }}

    /**
     * @param  list<string>  $decisionPath
     * @return array{{
     *     matched: bool,
     *     prediction: string|null,
     *     rule_code: string|null,
     *     decision_path: list<string>,
     *     confidence: float|null,
     *     support_count: int|null,
     *     support_percentage: float|null
     * }}
     */
    private function matched(
        string $prediction,
        string $ruleCode,
        array $decisionPath,
        float $confidence,
        int $supportCount,
        float $supportPercentage,
    ): array {{
        return [
            'matched' => true,
            'prediction' => $prediction,
            'rule_code' => $ruleCode,
            'decision_path' => $decisionPath,
            'confidence' => $confidence,
            'support_count' => $supportCount,
            'support_percentage' => $supportPercentage,
        ];
    }}

    /**
     * @param  list<string>  $decisionPath
     * @return array{{
     *     matched: bool,
     *     prediction: string|null,
     *     rule_code: string|null,
     *     decision_path: list<string>,
     *     confidence: float|null,
     *     support_count: int|null,
     *     support_percentage: float|null
     * }}
     */
    private function unmatched(array $decisionPath): array
    {{
        return [
            'matched' => false,
            'prediction' => null,
            'rule_code' => null,
            'decision_path' => $decisionPath,
            'confidence' => null,
            'support_count' => null,
            'support_percentage' => null,
        ];
    }}
}}
"""


def age_check_php(age_ops: list[tuple[str, float]], age_var: str = "$age") -> str:
    parts = []
    for op, val in age_ops:
        # RapidMiner thresholds: > 29.500 means age >= 30 for integer ages
        # For integer age: age > 29.5 <=> age >= 30; age <= 29.5 <=> age <= 29
        if op == ">":
            # age > threshold
            if val == int(val):
                parts.append(f"{age_var} > {int(val)}")
            else:
                # > 29.5 → >= ceil(29.5) = 30 → age >= 30
                import math
                parts.append(f"{age_var} >= {math.floor(val) + 1}")
        else:  # <=
            if val == int(val):
                parts.append(f"{age_var} <= {int(val)}")
            else:
                import math
                parts.append(f"{age_var} <= {math.floor(val)}")
    return " && ".join(parts)


def generate_classify_method(method: str, promil: str, items: list[dict], needs_action: bool) -> str:
    """Generate a classify* method using nested if/match from leaf conditions."""
    sig = f"private function {method}(int $age, string $action): array" if needs_action else f"private function {method}(int $age): array"

    # Build a simple decision by evaluating leaves in order - but need correct branching.
    # Better: reconstruct tree structure from age_ops prefixes.

    # For reliability, generate if-elseif chain that checks exact condition sets.
    # Order matters: more specific first. Sort by number of conditions descending.
    sorted_items = sorted(items, key=lambda x: (len(x["norm"]["age_ops"]) + (1 if x["norm"]["action_code"] else 0)), reverse=True)

    # Actually for mutual exclusivity of RapidMiner leaves, each leaf is unique path.
    # We need proper tree walk. Build nested structure.

    tree = build_nested(items)

    body = render_nested(tree, promil, indent=2)

    return f"""    /**
     * @return array{{
     *     matched: bool,
     *     prediction: string|null,
     *     rule_code: string|null,
     *     decision_path: list<string>,
     *     confidence: float|null,
     *     support_count: int|null,
     *     support_percentage: float|null
     * }}
     */
    {sig}
    {{
{body}
    }}
"""


def parse_step(condition: str) -> tuple:
    """Return ('age', op, val) | ('action', code, name) from a raw condition."""
    if condition.startswith("Umur > "):
        return ("age", ">", float(condition.split(" > ", 1)[1]))
    if condition.startswith("Umur <= "):
        return ("age", "<=", float(condition.split(" <= ", 1)[1]))
    if condition.startswith("Tindakan Hari Ini = "):
        name = condition.split(" = ", 1)[1]
        return ("action", to_action_code(name), name)
    raise ValueError(condition)


def merge_leaf(existing: dict, new: dict) -> dict:
    if existing["prediction"] != new["prediction"]:
        # Prefer larger support if conflict (should not happen for Kontrol USG/Usg)
        return new if new["support"] > existing["support"] else existing
    merged = dict(existing)
    merged["support"] = existing["support"] + new["support"]
    merged["majority"] = existing["majority"] + new["majority"]
    merged["confidence"] = round(merged["majority"] / merged["support"] * 100, 2)
    merged["support_pct"] = round(merged["support"] / TRAINING_COUNT * 100, 2)
    return merged


def build_nested(items: list[dict]) -> dict:
    """Build nested decision structure following each leaf's condition order."""
    root: dict = {"type": "node", "children": {}}

    for leaf in items:
        node = root
        # Skip promil condition (first)
        steps = [parse_step(c) for c in leaf["conditions_raw"] if not c.startswith("Promil Saat Ini")]

        for i, step in enumerate(steps):
            is_last = i == len(steps) - 1
            if step[0] == "age":
                _, op, val = step
                key = ("age", op, val)
                if key not in node["children"]:
                    node["children"][key] = {"type": "node", "children": {}}
                node = node["children"][key]
                if is_last:
                    node["children"][("leaf",)] = {"type": "leaf", "leaf": leaf}
            else:
                _, code, _name = step
                key = ("action",)
                if key not in node["children"]:
                    node["children"][key] = {"type": "actions", "cases": {}, "nested": {}}
                actions_node = node["children"][key]
                if is_last:
                    cases = actions_node["cases"]
                    if code in cases:
                        cases[code] = merge_leaf(cases[code], leaf)
                    else:
                        cases[code] = leaf
                else:
                    # action then further age splits
                    nested = actions_node["nested"]
                    if code not in nested:
                        nested[code] = {"type": "node", "children": {}}
                    node = nested[code]

    return root


def fmt_thresh(v: float) -> str:
    s = f"{v:.3f}".rstrip("0").rstrip(".")
    return s.replace(".", ",")


def render_nested(node: dict, promil: str, indent: int, path_so_far: list[str] | None = None) -> str:
    import math

    path_so_far = path_so_far or [f"Promil Saat Ini = {promil}"]
    sp = "    " * indent
    children = node.get("children", {})

    age_gt = None
    age_lte = None
    age_threshold = None
    actions = None
    leaf = None

    for key, child in children.items():
        if key[0] == "age":
            _, op, val = key
            age_threshold = val
            if op == ">":
                age_gt = child
            else:
                age_lte = child
        elif key[0] == "action":
            actions = child
        elif key[0] == "leaf":
            leaf = child["leaf"]

    lines: list[str] = []

    if age_gt is not None or age_lte is not None:
        if age_threshold != int(age_threshold):
            gt_check = f"$age >= {math.floor(age_threshold) + 1}"
        else:
            gt_check = f"$age > {int(age_threshold)}"

        path_gt = f"Umur > {fmt_thresh(age_threshold)}"
        path_lte = f"Umur <= {fmt_thresh(age_threshold)}"

        lines.append(f"{sp}if ({gt_check}) {{")
        if age_gt:
            lines.append(render_nested(age_gt, promil, indent + 1, path_so_far + [path_gt]))
        else:
            up = ", ".join(f"'{php_escape(p)}'" for p in path_so_far + [path_gt, "Tidak ditemukan decision rule yang sesuai"])
            lines.append(f"{sp}    return $this->unmatched([{up}]);")
        lines.append(f"{sp}}}")
        lines.append("")
        if age_lte:
            lines.append(render_nested(age_lte, promil, indent, path_so_far + [path_lte]))
        else:
            up = ", ".join(f"'{php_escape(p)}'" for p in path_so_far + [path_lte, "Tidak ditemukan decision rule yang sesuai"])
            lines.append(f"{sp}return $this->unmatched([{up}]);")
        return "\n".join(lines)

    if actions is not None:
        cases = actions.get("cases", {})
        nested = actions.get("nested", {})
        all_codes = list(OrderedDict.fromkeys([*cases.keys(), *nested.keys()]))

        lines.append(f"{sp}return match ($action) {{")
        for code in all_codes:
            path = path_so_far + [f"Tindakan Hari Ini = {code}"]
            if code in nested:
                # nested age after action — use block via IIFE-style not available; use match arm with expression?
                # PHP match arms must be expressions. Use a private helper or immediately-invoked logic.
                # Simplest: expand as nested match by calling a closure — PHP doesn't allow multi-statement in match easily.
                # Use match arm that calls an inline approach: for nested, emit a dedicated private method instead.
                # Workaround: don't use match; use if/elseif for action when nested exists.
                pass
        # If any nested actions, switch to if/elseif for clarity
        if nested:
            lines = []
            for code in all_codes:
                path = path_so_far + [f"Tindakan Hari Ini = {code}"]
                lines.append(f"{sp}if ($action === '{php_escape(code)}') {{")
                if code in nested:
                    lines.append(render_nested(nested[code], promil, indent + 1, path))
                else:
                    leaf_item = cases[code]
                    path_php = ", ".join(f"'{php_escape(p)}'" for p in path)
                    lines.append(
                        f"{sp}    return $this->leaf("
                        f"'{php_escape(leaf_item['prediction'])}', '{leaf_item['code']}', [{path_php}], "
                        f"{leaf_item['majority']}, {leaf_item['support']});"
                    )
                lines.append(f"{sp}}}")
                lines.append("")
            unmatched_path = path_so_far + [
                "Tindakan Hari Ini tidak memiliki cabang pada model aktif",
                "Tidak ditemukan decision rule yang sesuai",
            ]
            up = ", ".join(f"'{php_escape(p)}'" for p in unmatched_path)
            lines.append(f"{sp}return $this->unmatched([{up}]);")
            return "\n".join(lines)

        for code, leaf_item in cases.items():
            path = path_so_far + [f"Tindakan Hari Ini = {code}"]
            path_php = ", ".join(f"'{php_escape(p)}'" for p in path)
            lines.append(
                f"{sp}    '{php_escape(code)}' => $this->leaf("
                f"'{php_escape(leaf_item['prediction'])}', '{leaf_item['code']}', [{path_php}], "
                f"{leaf_item['majority']}, {leaf_item['support']}),"
            )
        unmatched_path = path_so_far + [
            "Tindakan Hari Ini tidak memiliki cabang pada model aktif",
            "Tidak ditemukan decision rule yang sesuai",
        ]
        up = ", ".join(f"'{php_escape(p)}'" for p in unmatched_path)
        lines.append(f"{sp}    default => $this->unmatched([{up}]),")
        lines.append(f"{sp}}};")
        return "\n".join(lines)

    if leaf is not None:
        path_php = ", ".join(f"'{php_escape(p)}'" for p in path_so_far)
        return (
            f"{sp}return $this->leaf("
            f"'{php_escape(leaf['prediction'])}', '{leaf['code']}', [{path_php}], "
            f"{leaf['majority']}, {leaf['support']});"
        )

    up = ", ".join(f"'{php_escape(p)}'" for p in path_so_far + ["Tidak ditemukan decision rule yang sesuai"])
    return f"{sp}return $this->unmatched([{up}]);"


def generate_seeder(leaves: list[dict]) -> str:
    actions: OrderedDict[str, str] = OrderedDict()
    # Keep HO and PRP if previously existed even if not in tree
    extras = [
        ("HO", "HO"),
        ("PRP_OVARIUM_TRANSVAGINAL", "PRP Ovarium Transvaginal"),
        ("LAINNYA", "Lainnya"),
    ]
    for leaf in leaves:
        name = leaf["norm"]["action_name"]
        if not name:
            continue
        code = leaf["norm"]["action_code"]
        if code not in actions or name == "Kontrol Usg":
            actions[code] = "Kontrol Usg" if code == "KONTROL_USG" else name

    items = list(actions.items())
    for code, name in extras:
        if code not in actions:
            items.append((code, name))

    # sort: known useful first? keep insertion then extras
    lines = []
    for i, (code, name) in enumerate(items, start=1):
        lines.append(
            f"            ['code' => '{code}', 'name' => '{php_escape(name)}', 'sort_order' => {i}],"
        )

    return f"""<?php

namespace Database\\Seeders;

use App\\Models\\CurrentAction;
use Illuminate\\Database\\Seeder;

class CurrentActionSeeder extends Seeder
{{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {{
        $items = [
{chr(10).join(lines)}
        ];

        foreach ($items as $item) {{
            CurrentAction::query()->updateOrCreate(
                ['code' => $item['code']],
                [
                    'name' => $item['name'],
                    'is_active' => true,
                    'sort_order' => $item['sort_order'],
                ],
            );
        }}
    }}
}}
"""


def main() -> None:
    text = TREE_PATH.read_text(encoding="utf-8")
    leaves = parse_tree(text)
    print(f"Parsed {len(leaves)} leaves")
    support_sum = sum(l["support"] for l in leaves)
    print(f"Support sum={support_sum} (training={TRAINING_COUNT})")

    # enrich norms early for seeder
    for i, leaf in enumerate(leaves, start=1):
        leaf["norm"] = normalize_conditions(leaf["conditions_raw"])
        leaf["code"] = f"R{i}"

    service = generate_service(leaves)
    SERVICE_PATH.write_text(service, encoding="utf-8")
    print(f"Wrote {SERVICE_PATH}")

    seeder = generate_seeder(leaves)
    SEEDER_PATH.write_text(seeder, encoding="utf-8")
    print(f"Wrote {SEEDER_PATH}")


if __name__ == "__main__":
    main()
