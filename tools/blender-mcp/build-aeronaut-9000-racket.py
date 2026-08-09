"""Build and export Li-Ning Aeronaut 9000 badminton racket GLB."""

import math
import os

import bpy

EXPORT_PATH = os.path.join(
    os.path.dirname(os.path.dirname(os.path.dirname(__file__))),
    "public",
    "models",
    "products",
    "li-ning-aeronaut-9000.glb",
)

TOTAL_LENGTH = 0.675
HEAD_WIDTH = 0.22
HEAD_HEIGHT = 0.265
FRAME_RADIUS = 0.0085
SHAFT_RADIUS = 0.0065
GRIP_RADIUS = 0.0125
GRIP_LENGTH = 0.19


def clear_scene():
    bpy.ops.object.select_all(action="SELECT")
    bpy.ops.object.delete(use_global=False)
    for block in (bpy.data.meshes, bpy.data.materials, bpy.data.curves):
        for item in list(block):
            block.remove(item)


def make_mat(name, color, roughness=0.25, metallic=0.15):
    mat = bpy.data.materials.new(name=name)
    mat.use_nodes = True
    bsdf = mat.node_tree.nodes.get("Principled BSDF")
    bsdf.inputs["Base Color"].default_value = (*color, 1.0)
    bsdf.inputs["Roughness"].default_value = roughness
    bsdf.inputs["Metallic"].default_value = metallic
    return mat


def assign_mat(obj, mat):
    if obj.data.materials:
        obj.data.materials[0] = mat
    else:
        obj.data.materials.append(mat)


def head_xy(t):
    x = math.sin(t) * HEAD_WIDTH * 0.5
    y = math.cos(t) * HEAD_HEIGHT * 0.5
    if y > 0:
        y *= 0.9
    return x, y


def curve_from_points(name, points, radius, mat):
    curve_data = bpy.data.curves.new(name, type="CURVE")
    curve_data.dimensions = "3D"
    curve_data.resolution_u = 48
    curve_data.bevel_depth = radius
    curve_data.bevel_resolution = 8

    spline = curve_data.splines.new("POLY")
    spline.points.add(len(points) - 1)
    for idx, (x, y, z) in enumerate(points):
        spline.points[idx].co = (x, y, z, 1.0)

    obj = bpy.data.objects.new(name, curve_data)
    bpy.context.collection.objects.link(obj)
    bpy.context.view_layer.objects.active = obj
    obj.select_set(True)
    bpy.ops.object.convert(target="MESH")
    assign_mat(obj, mat)
    return obj


def add_cylinder(name, loc, radius, depth, mat, axis="Y"):
    rot = (0, 0, 0) if axis == "Y" else (math.pi / 2, 0, 0)
    bpy.ops.mesh.primitive_cylinder_add(radius=radius, depth=depth, location=loc, rotation=rot, vertices=32)
    obj = bpy.context.active_object
    obj.name = name
    assign_mat(obj, mat)
    return obj


def build_head(mats):
    points = []
    for i in range(49):
        t = math.pi * 0.12 + (math.pi * 0.76 * i / 48.0)
        x, y = head_xy(t)
        points.append((x, y, 0.0))
    return curve_from_points("HeadFrame", points, FRAME_RADIUS, mats["white"])


def build_throat(mats):
    left = (-HEAD_WIDTH * 0.34, -HEAD_HEIGHT * 0.36, 0.0)
    right = (HEAD_WIDTH * 0.34, -HEAD_HEIGHT * 0.36, 0.0)
    center = (0.0, -HEAD_HEIGHT * 0.44, 0.0)
    return curve_from_points("Throat", [left, center, right], FRAME_RADIUS * 0.95, mats["white"])


def build_t_joint(mats):
    bpy.ops.mesh.primitive_cube_add(location=(0.0, -HEAD_HEIGHT * 0.41, 0.0), size=1.0)
    obj = bpy.context.active_object
    obj.name = "TJoint"
    obj.scale = (0.055, 0.028, 0.012)
    bpy.ops.object.transform_apply(scale=True)
    assign_mat(obj, mats["white"])
    return obj


def build_shaft_grip(mats):
    throat_y = -HEAD_HEIGHT * 0.46
    shaft_len = TOTAL_LENGTH - HEAD_HEIGHT * 0.55 - GRIP_LENGTH
    shaft = add_cylinder("Shaft", (0.0, throat_y - shaft_len * 0.5, 0.0), SHAFT_RADIUS, shaft_len, mats["white"])
    grip = add_cylinder("Grip", (0.0, throat_y - shaft_len - GRIP_LENGTH * 0.5, 0.0), GRIP_RADIUS, GRIP_LENGTH, mats["grip"])
    cap = add_cylinder("ButtCap", (0.0, throat_y - shaft_len - GRIP_LENGTH - 0.01, 0.0), GRIP_RADIUS * 0.92, 0.014, mats["white"])
    return [shaft, grip, cap]


def build_accents(mats):
    accents = []
    specs = [
        ("GoldTop", (0.0, HEAD_HEIGHT * 0.42, 0.003), (0.1, 0.022, 0.002), mats["gold"]),
        ("RedL", (-0.055, HEAD_HEIGHT * 0.12, 0.003), (0.028, 0.05, 0.002), mats["red"]),
        ("BlackR", (0.055, HEAD_HEIGHT * 0.12, 0.003), (0.028, 0.05, 0.002), mats["black"]),
        ("RedThroat", (0.0, -HEAD_HEIGHT * 0.39, 0.004), (0.018, 0.012, 0.002), mats["red"]),
    ]
    for name, loc, scale, mat in specs:
        bpy.ops.mesh.primitive_cube_add(location=loc, size=1.0)
        obj = bpy.context.active_object
        obj.name = name
        obj.scale = scale
        bpy.ops.object.transform_apply(scale=True)
        assign_mat(obj, mat)
        accents.append(obj)
    return accents


def build_strings(mats):
    strings = []
    top_y = HEAD_HEIGHT * 0.34
    bottom_y = -HEAD_HEIGHT * 0.22
    cols = 18
    rows = 22

    for i in range(cols):
        t = i / (cols - 1)
        x = -HEAD_WIDTH * 0.34 + HEAD_WIDTH * 0.68 * t
        strings.append(curve_from_points(
            f"SV_{i}",
            [(x, top_y, 0.0), (x, bottom_y, 0.0)],
            0.0003,
            mats["string"],
        ))

    for j in range(rows):
        t = j / (rows - 1)
        y = bottom_y + (top_y - bottom_y) * t
        half_w = (HEAD_WIDTH * 0.34) * math.sqrt(max(0.08, 1.0 - ((y / (HEAD_HEIGHT * 0.42)) ** 2)))
        strings.append(curve_from_points(
            f"SH_{j}",
            [(-half_w, y, 0.0), (half_w, y, 0.0)],
            0.0003,
            mats["string"],
        ))

    return strings


def build_grommets(mats):
    grommets = []
    for i in range(32):
        t = math.pi * 0.12 + (math.pi * 0.76 * i / 31.0)
        x, y = head_xy(t)
        scale = 1.02
        bpy.ops.mesh.primitive_uv_sphere_add(
            radius=0.0018,
            location=(x * scale, y * scale, 0.0),
            segments=8,
            ring_count=4,
        )
        obj = bpy.context.active_object
        obj.name = f"Grommet_{i}"
        assign_mat(obj, mats["black"])
        grommets.append(obj)
    return grommets


def export_scene(objects):
    os.makedirs(os.path.dirname(EXPORT_PATH), exist_ok=True)
    bpy.ops.object.select_all(action="DESELECT")
    for obj in objects:
        obj.select_set(True)
    bpy.context.view_layer.objects.active = objects[0]
    bpy.ops.export_scene.gltf(
        filepath=EXPORT_PATH,
        export_format="GLB",
        use_selection=True,
        export_apply=True,
        export_yup=True,
    )
    return EXPORT_PATH


def main():
    clear_scene()

    mats = {
        "white": make_mat("FrameWhite", (0.95, 0.95, 0.93), roughness=0.18, metallic=0.25),
        "gold": make_mat("FrameGold", (0.82, 0.68, 0.38), roughness=0.2, metallic=0.55),
        "red": make_mat("AccentRed", (0.82, 0.08, 0.12), roughness=0.35, metallic=0.05),
        "black": make_mat("AccentBlack", (0.04, 0.04, 0.04), roughness=0.4, metallic=0.1),
        "grip": make_mat("GripWrap", (0.9, 0.9, 0.88), roughness=0.85, metallic=0.0),
        "string": make_mat("StringWhite", (0.98, 0.98, 1.0), roughness=0.15, metallic=0.0),
    }

    parts = [
        build_head(mats),
        build_throat(mats),
        build_t_joint(mats),
        *build_shaft_grip(mats),
        *build_accents(mats),
        *build_grommets(mats),
        *build_strings(mats),
    ]

    path = export_scene(parts)
    print(f"Exported racket to {path}")


if __name__ == "__main__":
    main()
