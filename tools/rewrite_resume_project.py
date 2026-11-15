"""把 simple-book 个人记账系统的真实技术栈与职责写进简历对应条目。

源文件：C:\\Users\\10249\\Desktop\\刘天乐_web前端工程师_最新版.docx
输出：  output\\刘天乐_web前端工程师_最新版_已更新.docx
沿用文档中「高校社团管理系统」条目的段落格式（缩进、编号、Wingdings 项目符号）。
"""

import copy
from pathlib import Path

from docx import Document
from docx.text.paragraph import Paragraph

SRC = Path(r"C:\Users\10249\Desktop\刘天乐_web前端工程师_最新版.docx")
OUT = Path(__file__).resolve().parent.parent / "output" / "刘天乐_web前端工程师_最新版_已更新.docx"

TECH = (
    "Laravel 13 + PHP 8.3 + Blade + 原生 JavaScript + Bootstrap 5 + "
    "Chart.js + flatpickr + MySQL"
)

DESCRIPTION = (
    "该项目是一套面向个人用户的记账 Web 系统，用于解决日常收支记录零散、账单缺乏统计口径的问题。"
    "系统包含登录注册、数据仪表盘、账单管理、统计分析、分类管理、账户管理、个人中心与系统设置 8 个页面，"
    "支持收入、支出与账户间转账三类流水；分类采用一级/二级结构并支持预算设置，账户余额随账单增删改自动联动，"
    "统计可按本周/本月/本年/累计及自定义日期区间查看。前端使用 Blade 模板 + 原生 JavaScript 完成页面渲染与交互，"
    "按页面拆分 JS/CSS 并复用公共头部与侧边导航；后端基于 Laravel 13 的 MVC 分层，设计 users、categories、"
    "accounts、transactions、user_settings 5 张业务表，通过 FormRequest 校验参数、Eloquent 关联查询与数据库事务"
    "保证账单和账户余额一致，Chart.js 完成多维度图表展示。"
)

BULLETS = [
    "独立完成需求拆解与数据库设计，规划用户、分类、账户、账单、用户设置 5 张业务表；分类表通过 parent_id 自关联"
    "实现二级分类，账单表建立 user_id+type+date 联合索引支撑按类型、时间检索，外键按级联删除/置空策略保证关联数据一致；",

    "基于 Blade + 原生 JavaScript 完成 8 个页面开发，封装公共头部与侧边导航，按标题、脚本、样式按需传参复用；"
    "抽取 Toast 提示、金额与日期格式化、日期区间计算、CSV 导出（BOM 前缀解决 Excel 中文乱码）等公共方法，降低各页面重复代码；",

    "封装统一请求层 apiRequest，自动注入 CSRF Token 与 JSON 请求头，统一解包响应并集中处理 401 未登录、"
    "422 字段校验异常，配合后端 FormRequest 定位到具体表单项提示；完成登录注册、分类管理、账户管理的接口联调，"
    "并补齐账单列表分页（类型/日期/分类筛选 + 收支汇总）与增删改接口；",

    "使用 Chart.js 完成收支趋势折线图、支出/收入分类环形图、账户支出分布与月度对比柱状图，支持本周/本月/本年/"
    "累计/自定义区间（flatpickr 日期范围）切换，并按分类汇总生成支出排行与占比列表；",

    "后端参与账单与账户模块开发：账单新增/修改/删除均在数据库事务内锁定账户行，先回滚原金额再写入新金额，"
    "账户转账校验余额与同账户限制，保证账单与账户余额强一致；统计接口按用户维度聚合，并对账单归属做校验，拦截越权操作。",
]


def replace_format(target: Paragraph, template: Paragraph) -> None:
    """用模板段落的 pPr 覆盖目标段落格式，保留目标段落里的文字 run。"""
    old = target._p.pPr
    if old is not None:
        target._p.remove(old)
    if template._p.pPr is not None:
        target._p.insert(0, copy.deepcopy(template._p.pPr))


def main() -> None:
    doc = Document(str(SRC))
    ps = doc.paragraphs

    # 确认定位到的就是「个人记账系统」条目，避免改错段落
    assert ps[26].text.strip() == "项目：个人记账系统", ps[26].text
    assert ps[27].text.startswith("涉及技术："), ps[27].text
    assert ps[28].text.strip() == "项目描述：", ps[28].text
    assert "项目职责：" in ps[33].text, ps[33].text

    body_tpl = ps[19]     # 项目描述正文格式
    duty_tpl = ps[20]     # 项目职责：标题（带项目符号）
    bullet_tpl = ps[21]   # 职责条目格式，runs[0] 为 Wingdings 项目符号
    assert len(bullet_tpl.runs) == 2 and bullet_tpl.runs[0].font.name == "Wingdings"

    tech_p, body_p, duty_p = ps[27], ps[29], ps[33]
    old_bullets = list(ps[34:42])

    # 1) 技术栈
    assert len(tech_p.runs) == 2
    tech_p.runs[1].text = TECH

    # 2) 项目描述正文：只保留首个 run，改写文本并套用模板格式
    for extra in body_p.runs[1:]:
        extra._element.getparent().remove(extra._element)
    body_p.runs[0].text = DESCRIPTION
    replace_format(body_p, body_tpl)

    # 3) 删除原短信平台描述的多余段落
    for p in (ps[30], ps[31], ps[32]):
        p._element.getparent().remove(p._element)

    # 4) 项目职责标题：去掉原 Wingdings 箭头，改为与上一项目一致的列表符号
    if duty_p.runs[0].font.name == "Wingdings":
        duty_p.runs[0]._element.getparent().remove(duty_p.runs[0]._element)
    assert duty_p.runs[0].text == "项目职责："
    replace_format(duty_p, duty_tpl)

    # 5) 职责条目：原 8 条替换为 5 条，逐条插到「项目职责」之后
    for p in old_bullets:
        p._element.getparent().remove(p._element)

    anchor = duty_p._p
    for text in BULLETS:
        new_el = copy.deepcopy(bullet_tpl._p)
        para = Paragraph(new_el, bullet_tpl._parent)
        para.runs[1].text = text
        anchor.addnext(new_el)
        anchor = new_el

    OUT.parent.mkdir(parents=True, exist_ok=True)
    doc.save(str(OUT))
    print("saved:", OUT)


if __name__ == "__main__":
    main()
