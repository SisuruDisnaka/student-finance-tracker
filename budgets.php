<?php
require 'includes/config.php';

if($_POST){
    if($_POST['action'] == 'delete'){
        // the expenses of this category are deleted too
        q('DELETE FROM categories WHERE id=? AND user_id=?', [$_POST['id'], $uid]);
        flash('Category deleted.');
    }else{
        $name = trim($_POST['name']);
        $budget = $_POST['budget_amount'];

        if($name == ''){
            flash('Category name is required.', 'error');
        }elseif(!is_numeric($budget) || $budget < 0 || $budget >= 10000000000){
            flash('Budget must be 0 or more.', 'error');
        }elseif(val('SELECT COUNT(*) FROM categories WHERE user_id=? AND name=? AND id<>?', [$uid, $name, $_POST['id']])){
            flash('You already have a category with that name.', 'error');
        }elseif($_POST['id'] > 0){
            q('UPDATE categories SET name=?, budget_amount=? WHERE id=? AND user_id=?', [$name, $budget, $_POST['id'], $uid]);
            flash('Budget updated.');
        }else{
            q('INSERT INTO categories (user_id, name, budget_amount) VALUES (?,?,?)', [$uid, $name, $budget]);
            flash('Category added.');
        }
    }
    back();
}
include 'includes/header.php';

$list = cats($like);
?>

<div class="page-head">
    <div><h2>Budgets</h2><p class="muted">Set a monthly budget for each category.</p></div>
    <div class="head-actions">
        <form class="inline-form"><?= month_select() ?></form>
        <button class="btn btn-primary" data-modal="budgetModal">+ Add Category</button>
    </div>
</div>


