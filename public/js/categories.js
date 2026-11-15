// 分类管理脚本 - 接入后端 API
let expenseCategories = [];
let incomeCategories = [];

const icons = ['fa-utensils', 'fa-coffee', 'fa-bus', 'fa-subway', 'fa-taxi', 'fa-shopping-bag', 'fa-tshirt', 'fa-laptop', 'fa-film', 'fa-gamepad', 'fa-music', 'fa-hospital', 'fa-graduation-cap', 'fa-wallet', 'fa-gift', 'fa-chart-line'];
const colors = ['#ff6b6b', '#4ecdc4', '#ffa500', '#9b59b6', '#e74c3c', '#3498db', '#f39c12', '#28a745', '#95a5a6'];

let currentTab = 'expense', editingCategory = null, editingSubcategory = null, editingParentId = null,
    currentDeleteId = null, currentDeleteType = null, currentDeleteIsSub = false,
    selectedIcon = null, selectedColor = null, expandedCategories = new Set();

// ==================== 图标/颜色选择器 ====================
function renderIconPicker() {
    document.getElementById('iconPicker').innerHTML = icons.map(icon =>
        `<div class="icon-option" data-icon="${icon}" onclick="selectIcon(this, '${icon}')"><i class="fas ${icon}"></i></div>`
    ).join('');
}
function renderColorPicker() {
    document.getElementById('colorPicker').innerHTML = colors.map(color =>
        `<div class="color-option" style="background: ${color};" data-color="${color}" onclick="selectColor(this, '${color}')"></div>`
    ).join('');
}
function selectIcon(element, icon) {
    document.querySelectorAll('.icon-option').forEach(opt => opt.classList.remove('selected'));
    element.classList.add('selected');
    selectedIcon = icon;
}
function selectColor(element, color) {
    document.querySelectorAll('.color-option').forEach(opt => opt.classList.remove('selected'));
    element.classList.add('selected');
    selectedColor = color;
}
function applyPickerSelections() {
    document.querySelectorAll('.icon-option').forEach(opt => {
        opt.classList.toggle('selected', opt.dataset.icon === selectedIcon);
    });
    document.querySelectorAll('.color-option').forEach(opt => {
        opt.classList.toggle('selected', opt.dataset.color === selectedColor);
    });
}

// ==================== 加载数据 ====================
async function loadCategories() {
    try {
        const [expRes, incRes] = await Promise.all([
            apiRequest('/api/categories?type=expense'),
            apiRequest('/api/categories?type=income'),
        ]);
        expenseCategories = mapCategories(expRes.data);
        incomeCategories = mapCategories(incRes.data);
        renderCategories();
    } catch (e) {
        showToast('加载分类失败：' + e.message, 'error');
    }
}

function mapCategories(data) {
    return (data || []).map(item => ({
        id: item.id,
        name: item.name,
        icon: item.icon,
        color: item.color,
        budget: item.budget || null,
        count: item.count || 0,
        subcategories: (item.subcategories || []).map(sub => ({
            id: sub.id,
            name: sub.name,
            icon: sub.icon,
            color: sub.color,
            budget: sub.budget || null,
            count: sub.count || 0,
        })),
    }));
}

// ==================== 渲染列表 ====================
function toggleCategory(categoryId) {
    expandedCategories.has(categoryId) ? expandedCategories.delete(categoryId) : expandedCategories.add(categoryId);
    renderCategories();
}

function renderCategories() {
    document.getElementById('expenseCategoriesList').innerHTML = renderCategoryList(expenseCategories, 'expense');
    document.getElementById('incomeCategoriesList').innerHTML = renderCategoryList(incomeCategories, 'income');
}

function renderCategoryList(list, type) {
    if (list.length === 0) {
        return `<div class="empty-state"><i class="fas fa-folder-open"></i><p>暂无分类</p><small>点击"添加一级分类"创建</small></div>`;
    }
    return list.map(cat => {
        const expanded = expandedCategories.has(cat.id);
        const subHtml = cat.subcategories.length > 0
            ? cat.subcategories.map(sub => `
                <div class="subcategory-item">
                    <div class="subcategory-left">
                        <div class="sub-icon" style="background: ${sub.color || cat.color};"><i class="fas ${sub.icon}"></i></div>
                        <div class="sub-info">
                            <div class="sub-name">${sub.name}</div>
                            ${sub.budget ? `<div class="sub-count">预算 ¥${sub.budget}</div>` : ''}
                            <div class="sub-count">${sub.count} 笔账单</div>
                        </div>
                    </div>
                    <div class="sub-actions">
                        <button class="btn-edit-cat" onclick="editSubcategory('${type}', ${cat.id}, ${sub.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn-delete-cat" onclick="showDeleteModal('${type}', ${sub.id}, '${sub.name}', true)"><i class="fas fa-trash"></i></button>
                    </div>
                </div>`).join('')
            : `<div class="empty-state" style="padding:20px;"><i class="fas fa-plus-circle"></i><p>暂无子分类</p></div>`;

        return `
        <div class="category-card">
            <div class="category-header ${expanded ? 'expanded' : ''}" onclick="toggleCategory(${cat.id})">
                <div class="category-info">
                    <div class="category-icon" style="background: ${cat.color};"><i class="fas ${cat.icon}"></i></div>
                    <div class="category-details">
                        <div class="category-name">${cat.name}</div>
                        <div class="category-stats">
                            <span><i class="fas fa-receipt"></i> ${cat.count} 笔</span>
                            ${cat.budget ? `<span><i class="fas fa-chart-line"></i> 预算 ¥${cat.budget}</span>` : ''}
                            <span><i class="fas fa-layer-group"></i> ${cat.subcategories.length} 个子分类</span>
                        </div>
                    </div>
                </div>
                <div class="category-actions" onclick="event.stopPropagation()">
                    <button class="add-sub-btn" onclick="openAddModal('${type}', ${cat.id})"><i class="fas fa-plus"></i> 子分类</button>
                    <button class="btn-edit-cat" onclick="editCategory('${type}', ${cat.id})"><i class="fas fa-edit"></i></button>
                    <button class="btn-delete-cat" onclick="showDeleteModal('${type}', ${cat.id}, '${cat.name}', false)"><i class="fas fa-trash"></i></button>
                </div>
                <div class="expand-icon"><i class="fas fa-chevron-down"></i></div>
            </div>
            <div class="subcategories-list ${expanded ? 'show' : ''}">
                <div class="subcategory-grid">${subHtml}</div>
            </div>
        </div>`;
    }).join('');
}

// ==================== 新增弹窗 ====================
function openAddModal(type, parentId) {
    currentTab = type;
    editingCategory = null;
    editingSubcategory = null;
    editingParentId = parentId;
    selectedIcon = null;
    selectedColor = null;
    document.getElementById('modalTitle').innerHTML = parentId
        ? `添加${type === 'expense' ? '支出' : '收入'}子分类`
        : `添加${type === 'expense' ? '支出' : '收入'}一级分类`;
    document.getElementById('budgetDiv').style.display = 'block';
    document.getElementById('categoryName').value = '';
    document.getElementById('categoryBudget').value = '';
    renderIconPicker();
    renderColorPicker();
    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}

// ==================== 编辑弹窗 ====================
function editCategory(type, id) {
    currentTab = type;
    editingCategory = id;
    editingSubcategory = null;
    editingParentId = null;
    const categories = type === 'expense' ? expenseCategories : incomeCategories;
    const cat = categories.find(c => c.id === id);
    if (!cat) return;

    document.getElementById('modalTitle').innerHTML = `编辑${type === 'expense' ? '支出' : '收入'}分类`;
    document.getElementById('categoryName').value = cat.name;
    document.getElementById('categoryBudget').value = cat.budget || '';
    document.getElementById('budgetDiv').style.display = 'block';
    selectedIcon = cat.icon;
    selectedColor = cat.color;
    renderIconPicker();
    renderColorPicker();
    applyPickerSelections();
    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}

function editSubcategory(type, parentId, subId) {
    currentTab = type;
    editingCategory = null;
    editingSubcategory = subId;
    editingParentId = parentId;
    const categories = type === 'expense' ? expenseCategories : incomeCategories;
    const parent = categories.find(c => c.id === parentId);
    if (!parent) return;
    const sub = parent.subcategories.find(s => s.id === subId);
    if (!sub) return;

    document.getElementById('modalTitle').innerHTML = `编辑${type === 'expense' ? '支出' : '收入'}子分类`;
    document.getElementById('categoryName').value = sub.name;
    document.getElementById('categoryBudget').value = '';
    document.getElementById('categoryBudget').value = sub.budget || '';
    document.getElementById('budgetDiv').style.display = 'block';
    selectedIcon = sub.icon;
    selectedColor = sub.color || parent.color;
    renderIconPicker();
    renderColorPicker();
    applyPickerSelections();
    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}

// ==================== 保存（新增/编辑）====================
async function saveCategory() {
    const name = document.getElementById('categoryName').value.trim();
    const budget = parseFloat(document.getElementById('categoryBudget').value);
    if (!name) { showToast('请输入分类名称', 'error'); return; }
    if (!selectedIcon) { showToast('请选择图标', 'error'); return; }
    if (!selectedColor) { showToast('请选择颜色', 'error'); return; }

    try {
        // 编辑子分类
        if (editingSubcategory !== null) {
            const res = await apiRequest(`/api/categories/${editingSubcategory}`, {
                method: 'PUT',
                body: JSON.stringify({ name, icon: selectedIcon, color: selectedColor, type: currentTab, ...(!isNaN(budget) && budget > 0 ? { budget } : {}) }),
            });
            const updated = res.data;
            const categories = currentTab === 'expense' ? expenseCategories : incomeCategories;
            const parent = categories.find(c => c.id === editingParentId);
            if (parent) {
                const sub = parent.subcategories.find(s => s.id === editingSubcategory);
                if (sub) { sub.name = updated.name; sub.icon = updated.icon; sub.color = updated.color; sub.budget = updated.budget; }
            }
            showToast('子分类修改成功！', 'success');

            // 编辑一级分类
        } else if (editingCategory !== null) {
            const payload = { name, icon: selectedIcon, color: selectedColor, type: currentTab };
            if (!isNaN(budget) && budget > 0) payload.budget = budget;
            const res = await apiRequest(`/api/categories/${editingCategory}`, {
                method: 'PUT',
                body: JSON.stringify(payload),
            });
            const updated = res.data;
            const categories = currentTab === 'expense' ? expenseCategories : incomeCategories;
            const cat = categories.find(c => c.id === editingCategory);
            if (cat) { cat.name = updated.name; cat.icon = updated.icon; cat.color = updated.color; cat.budget = updated.budget; }
            showToast('分类修改成功！', 'success');

            // 新增子分类
        } else if (editingParentId !== null) {
            const res = await apiRequest('/api/categories', {
                method: 'POST',
                body: JSON.stringify({ name, icon: selectedIcon, color: selectedColor, type: currentTab, parent_id: editingParentId, ...(!isNaN(budget) && budget > 0 ? { budget } : {}) }),
            });
            const newSub = res.data;
            const categories = currentTab === 'expense' ? expenseCategories : incomeCategories;
            const parent = categories.find(c => c.id === editingParentId);
            if (parent) {
                parent.subcategories.push({ id: newSub.id, name: newSub.name, icon: newSub.icon, color: newSub.color, budget: newSub.budget || null, count: 0 });
                expandedCategories.add(editingParentId);
            }
            showToast('子分类添加成功！', 'success');

            // 新增一级分类
        } else {
            const payload = { name, icon: selectedIcon, color: selectedColor, type: currentTab };
            if (!isNaN(budget) && budget > 0) payload.budget = budget;
            const res = await apiRequest('/api/categories', {
                method: 'POST',
                body: JSON.stringify(payload),
            });
            const newCat = res.data;
            const item = { id: newCat.id, name: newCat.name, icon: newCat.icon, color: newCat.color, budget: newCat.budget, count: 0, subcategories: [] };
            currentTab === 'expense' ? expenseCategories.push(item) : incomeCategories.push(item);
            showToast('分类添加成功！', 'success');
        }

        bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
        renderCategories();
    } catch (e) {
        showToast(e.message, 'error');
    }
}

// ==================== 删除 ====================
function showDeleteModal(type, id, name, isSub) {
    currentDeleteType = type;
    currentDeleteId = id;
    currentDeleteIsSub = isSub;
    document.getElementById('deleteCategoryName').innerHTML = name;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', async function () {
    if (!currentDeleteId) return;
    const btn = this;
    btn.disabled = true;
    try {
        await apiRequest(`/api/categories/${currentDeleteId}`, { method: 'DELETE' });

        if (currentDeleteIsSub) {
            const categories = currentDeleteType === 'expense' ? expenseCategories : incomeCategories;
            for (let cat of categories) {
                const idx = cat.subcategories.findIndex(s => s.id === currentDeleteId);
                if (idx !== -1) { cat.subcategories.splice(idx, 1); break; }
            }
        } else {
            if (currentDeleteType === 'expense') {
                expenseCategories = expenseCategories.filter(c => c.id !== currentDeleteId);
            } else {
                incomeCategories = incomeCategories.filter(c => c.id !== currentDeleteId);
            }
        }
        showToast('删除成功！', 'success');
        bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
        renderCategories();
        currentDeleteId = null;
    } catch (e) {
        showToast(e.message, 'error');
    } finally {
        btn.disabled = false;
    }
});

// ==================== Tab 切换 ====================
function switchTab(type) {
    currentTab = type;
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`.tab-btn:${type === 'expense' ? 'first-child' : 'last-child'}`).classList.add('active');
    document.getElementById('expenseTab').classList.toggle('active', type === 'expense');
    document.getElementById('incomeTab').classList.toggle('active', type === 'income');
}

// ==================== 初始化 ====================
renderIconPicker();
renderColorPicker();
loadCategories();
