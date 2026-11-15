"""在简历「职业技能」中补充 PHP + Laravel 一条，位置紧随 Node.js + Express 之后。

就地更新：C:\\Users\\10249\\Desktop\\刘天乐_web前端工程师_最新版_已更新.docx
（桌面上的原始「_最新版.docx」保持不动）
"""

import copy
from pathlib import Path

from docx import Document
from docx.text.paragraph import Paragraph

DOCX = Path(r"C:\Users\10249\Desktop\刘天乐_web前端工程师_最新版_已更新.docx")

SKILL = (
    "熟悉 PHP 8 与 Laravel 框架，理解 MVC 分层、路由与中间件机制，掌握 Eloquent 模型关联、"
    "FormRequest 参数校验、数据库事务与迁移，能基于 Laravel 独立完成 RESTful 接口与后台业务开发；"
)

ANCHOR = "熟悉 Node.js + Express"


def main() -> None:
    doc = Document(str(DOCX))

    anchor = next(p for p in doc.paragraphs if p.text.startswith(ANCHOR))
    assert len(anchor.runs) == 1, [r.text for r in anchor.runs]

    new_el = copy.deepcopy(anchor._p)
    para = Paragraph(new_el, anchor._parent)
    para.runs[0].text = SKILL

    anchor._p.addnext(new_el)
    doc.save(str(DOCX))

    chk = Document(str(DOCX))
    idx = next(i for i, p in enumerate(chk.paragraphs) if p.text.startswith(ANCHOR))
    print("saved:", DOCX)
    for i in range(idx - 1, idx + 3):
        print(i, "|", chk.paragraphs[i].text[:60])


if __name__ == "__main__":
    main()
