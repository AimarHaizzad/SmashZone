"""Build and export the SmashZone 6-court advanced facility GLB."""

import bpy
import os

COURT_W = 6.1
COURT_D = 13.4
GAP = 2.4
ROWS = 2
COLS = 3
COURT_H = 0.08

EXPORT_PATH = os.path.join(
    os.path.dirname(os.path.dirname(os.path.dirname(__file__))),
    "public",
    "models",
    "smashzone-facility-6courts.glb",
)


def make_mat(name, color, roughness=0.5, metallic=0.0, emit=0.0):
    mat = bpy.data.materials.new(name=name)
    mat.use_nodes = True
    bsdf = mat.node_tree.nodes.get("Principled BSDF")
    bsdf.inputs["Base Color"].default_value = (*color, 1.0)
    bsdf.inputs["Roughness"].default_value = roughness
    bsdf.inputs["Metallic"].default_value = metallic
    if emit > 0:
        bsdf.inputs["Emission Color"].default_value = (*color, 1.0)
        bsdf.inputs["Emission Strength"].default_value = emit
    return mat


def assign_mat(obj, mat):
    if obj.data.materials:
        obj.data.materials[0] = mat
    else:
        obj.data.materials.append(mat)


def add_box(name, loc, scale, mat):
    bpy.ops.mesh.primitive_cube_add(location=loc)
    obj = bpy.context.active_object
    obj.name = name
    obj.scale = scale
    bpy.ops.object.transform_apply(scale=True)
    assign_mat(obj, mat)
    return obj


def build_court(court_num, cx, cy, mats):
    root_name = f"Court_{court_num}"
    court = add_box(
        root_name,
        (cx, cy, COURT_H / 2),
        (COURT_W / 2, COURT_D / 2, COURT_H / 2),
        mats["court"],
    )

    hw, hd = COURT_W / 2, COURT_D / 2
    z = COURT_H + 0.004

    def line(name, lx, ly, sx, sy):
        add_box(name, (lx, ly, z), (sx / 2, sy / 2, 0.008), mats["line"])

    line(f"{root_name}_Line_Outer_T", cx, cy + hd - 0.04, COURT_W - 0.08, 0.05)
    line(f"{root_name}_Line_Outer_B", cx, cy - hd + 0.04, COURT_W - 0.08, 0.05)
    line(f"{root_name}_Line_Outer_L", cx - hw + 0.04, cy, 0.05, COURT_D - 0.08)
    line(f"{root_name}_Line_Outer_R", cx + hw - 0.04, cy, 0.05, COURT_D - 0.08)
    line(f"{root_name}_Line_Center", cx, cy, COURT_W - 0.08, 0.05)

    svc = 1.98
    line(f"{root_name}_Line_Svc_T", cx, cy + svc, COURT_W - 0.08, 0.04)
    line(f"{root_name}_Line_Svc_B", cx, cy - svc, COURT_W - 0.08, 0.04)

    inset = 0.46
    line(f"{root_name}_Line_Single_L", cx - hw + inset, cy, 0.04, COURT_D - 0.08)
    line(f"{root_name}_Line_Single_R", cx + hw + inset, cy, 0.04, COURT_D - 0.08)

    post_x = hw - 0.15
    for side, sx in [("L", -post_x), ("R", post_x)]:
        add_box(f"{root_name}_Post_{side}", (cx + sx, cy, 0.78), (0.04, 0.04, 0.78), mats["post"])

    add_box(f"{root_name}_Net", (cx, cy, 0.78), (COURT_W / 2 - 0.2, 0.02, 0.75), mats["net"])
    add_box(f"{root_name}_Net_Tape", (cx, cy, 1.55), (COURT_W / 2 - 0.15, 0.03, 0.02), mats["line"])
    add_box(
        f"{root_name}_Zone_T",
        (cx, cy + hd - 1.0, COURT_H + 0.002),
        (COURT_W / 2 - 0.1, 0.9, 0.001),
        mats["accent"],
    )
    add_box(
        f"{root_name}_Zone_B",
        (cx, cy - hd + 1.0, COURT_H + 0.002),
        (COURT_W / 2 - 0.1, 0.9, 0.001),
        mats["accent"],
    )

    return court


def main():
    bpy.ops.object.select_all(action="SELECT")
    bpy.ops.object.delete(use_global=False)

    mats = {
        "floor": make_mat("Floor_Concrete", (0.22, 0.24, 0.27), 0.85),
        "court": make_mat("Court_Surface", (0.10, 0.48, 0.28), 0.35),
        "line": make_mat("Court_Line", (0.98, 0.98, 0.98), 0.25),
        "net": make_mat("Net_Mesh", (0.85, 0.88, 0.82), 0.6),
        "post": make_mat("Net_Post", (0.12, 0.12, 0.14), 0.4, 0.6),
        "wall": make_mat("Wall", (0.32, 0.35, 0.40), 0.7),
        "accent": make_mat("Accent_Emerald", (0.05, 0.55, 0.35), 0.3, 0.0, 0.15),
        "light_panel": make_mat("Light_Panel", (1.0, 0.98, 0.92), 0.2, 0.0, 2.5),
        "bench": make_mat("Bench_Wood", (0.45, 0.28, 0.14), 0.55),
    }

    fac_w = COLS * COURT_W + (COLS - 1) * GAP + 8
    fac_d = ROWS * COURT_D + (ROWS - 1) * GAP + 10
    hw, hd = fac_w / 2, fac_d / 2

    add_box("Facility_Floor", (0, 0, -0.06), (hw, hd, 0.06), mats["floor"])

    start_x = -((COLS - 1) * (COURT_W + GAP)) / 2
    start_y = ((ROWS - 1) * (COURT_D + GAP)) / 2

    for i in range(1, 7):
        row = (i - 1) // COLS
        col = (i - 1) % COLS
        cx = start_x + col * (COURT_W + GAP)
        cy = start_y - row * (COURT_D + GAP)
        build_court(i, cx, cy, mats)

    wall_h = 2.8
    wall_t = 0.25
    add_box("Wall_North", (0, hd + 0.5, wall_h / 2), (hw, wall_t, wall_h / 2), mats["wall"])
    add_box("Wall_South", (0, -hd - 0.5, wall_h / 2), (hw, wall_t, wall_h / 2), mats["wall"])
    add_box("Wall_East", (hw + 0.5, 0, wall_h / 2), (wall_t, hd, wall_h / 2), mats["wall"])
    add_box("Wall_West", (-hw - 0.5, 0, wall_h / 2), (wall_t, hd, wall_h / 2), mats["wall"])

    for i, x in enumerate([-8, -2.5, 2.5, 8]):
        add_box(f"Bench_{i + 1}", (x, -hd + 1.2, 0.25), (1.8, 0.25, 0.25), mats["bench"])
        add_box(f"Bench_{i + 1}_Leg_L", (x - 1.5, -hd + 1.2, 0.12), (0.08, 0.2, 0.12), mats["post"])
        add_box(f"Bench_{i + 1}_Leg_R", (x + 1.5, -hd + 1.2, 0.12), (0.08, 0.2, 0.12), mats["post"])

    add_box("Entrance_Sign", (0, hd + 0.15, 2.2), (4.5, 0.12, 0.6), mats["accent"])

    bpy.ops.object.select_all(action="DESELECT")
    for obj in bpy.data.objects:
        if obj.type == "MESH":
            obj.select_set(True)

    os.makedirs(os.path.dirname(EXPORT_PATH), exist_ok=True)
    bpy.ops.export_scene.gltf(
        filepath=EXPORT_PATH,
        export_format="GLB",
        use_selection=True,
        export_apply=True,
        export_yup=True,
    )
    print(f"Exported advanced facility to {EXPORT_PATH}")


if __name__ == "__main__":
    main()
