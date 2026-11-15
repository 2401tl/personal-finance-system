@include('coms.header', [
    'title' => '分类管理',
    'imports' => ['css/categories.css'],
])

<div class="container-fluid">
    <div class="row">
        @include('coms.left-nav', ['active' => 'categories'])

        <div class="col-md-9 col-lg-10 main-content">
            @include('coms.top-nav', ['pageTitle' => '分类管理'])

            <div class="tabs-container">
                <div class="tabs-header">
                    <button class="tab-btn active" onclick="switchTab('expense')"><i class="fas fa-arrow-down"></i> 支出分类</button>
                    <button class="tab-btn" onclick="switchTab('income')"><i class="fas fa-arrow-up"></i> 收入分类</button>
                </div>
                <div id="expenseTab" class="tab-content active">
                    <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
                        <button class="add-btn" onclick="openAddModal('expense', null)"><i class="fas fa-plus"></i> 添加一级分类</button>
                    </div>
                    <div id="expenseCategoriesList" class="categories-grid"></div>
                </div>
                <div id="incomeTab" class="tab-content">
                    <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
                        <button class="add-btn" onclick="openAddModal('income', null)"><i class="fas fa-plus"></i> 添加一级分类</button>
                    </div>
                    <div id="incomeCategoriesList" class="categories-grid"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="modalTitle">添加分类</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">分类名称 *</label><input type="text" id="categoryName" class="form-control" placeholder="请输入分类名称"></div>
                <div class="mb-3"><label class="form-label">选择图标 *</label><div class="icon-picker" id="iconPicker"></div></div>
                <div class="mb-3"><label class="form-label">选择颜色 *</label><div class="color-picker" id="colorPicker"></div></div>
                <div class="mb-3" id="budgetDiv"><label class="form-label">预算（可选）</label><input type="number" id="categoryBudget" class="form-control" placeholder="每月预算金额" step="0.01"></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button><button type="button" class="btn btn-primary" onclick="saveCategory()">保存</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">确认删除</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p>确定要删除分类 <strong id="deleteCategoryName"></strong> 吗？</p><p class="text-danger" id="deleteWarning">注意：删除分类后，该分类下的账单将变为"未分类"！</p></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button><button type="button" class="btn btn-danger" id="confirmDeleteBtn">确认删除</button></div></div></div>
</div>

@include('coms.footer', ['imports' => ['js/categories.js']])
