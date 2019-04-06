<?php 
use app\modules\news\admin\Module as AdminModule;
?>

<div ng-controller="MenuController" class="menu-management">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active"><?= AdminModule::t('Edit menu')?></a>
        </li>
        <li class="nav-item" ng-show="data.menu.length">
            <a class="nav-link"><?= AdminModule::t('Manage position')?></a>
        </li>
    </ul>
    <div class="alert alert-light" role="alert">
        <span ng-hide="data.menus.length > 0">
            <?= AdminModule::t('Edit your menu below, or')?> 
        </span>
        <span ng-show="data.menus.length > 0">
            <?= AdminModule::t('Select a menu to edit:')?> 
            <select ng-options="item as item.name for item in data.menus track by item.id" ng-model="selected" ng-change="selectItem(item)"></select> 
            <button class="btn btn-sm btn-primary" ng-click="selectMenu()">Select</button>
        </span>
        <button class="btn text-danger btn-link" ng-click="createMenu()"><?= AdminModule::t('create a new menu')?></button>.
    </div>

    <div class="luya-main" ng-class="{'luya-mainnav-is-open' : isHover}">
        <div class="luya-subnav menu-resources" ng-class="{'overlaying': liveEditStateToggler}">
            <div class="card">
                <div class="card-header" ng-click="toggle('pages')">
                    <?= AdminModule::t('Pages')?>
                    <i class="material-icons" ng-hide="collapseExpaned.pages">arrow_drop_down</i>
                    <i class="material-icons" ng-show="collapseExpaned.pages">arrow_drop_up</i>
                </div>
                <div class="card-block animate-show-hide" ng-show="collapseExpaned.pages">
                    <ul class="form-group">
                        <li ng-repeat="item in data.pages">
                            <div class="form-check" ng-class="{'form-check-active': item.checked}">
                                <input id="chk-page-{{item.id}}" type="checkbox" ng-click="item.checked=!item.checked" ng-checked="item.checked" class="form-check-input">
                                <label for="chk-page-{{item.id}}">{{item.name}}</label>
                            </div>
                        </li>
                    </ul>
                    <div class="text-right form-group">
                        <button ng-disabled="!data.menu.id || !getCheckedItems('pages').length" ng-click="addToMenu('pages')" type="button" class="btn btn-sm btn-add"><?= AdminModule::t('Add To Menu')?></button>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" ng-click="toggle('categories')">
                    <?= AdminModule::t('Categories')?>
                    <i class="material-icons" ng-hide="collapseExpaned.categories">arrow_drop_down</i>
                    <i class="material-icons" ng-show="collapseExpaned.categories">arrow_drop_up</i>
                </div>
                <div class="card-block animate-show-hide" ng-show="collapseExpaned.categories">
                    <ul class="form-group">
                        <li ng-repeat="item in data.categories">
                            <div class="form-check level{{ item.level }}" ng-class="{'form-check-active': item.checked}">
                                <input id="chk-category-{{ item.id }}" type="checkbox" ng-click="item.checked=!item.checked" ng-checked="item.checked" class="form-check-input">
                                <label for="chk-category-{{ item.id }}">{{item.name}}</label>
                            </div>
                        </li>
                    </ul>
                    <div class="text-right form-group">
                        <button ng-disabled="!data.menu.id || !getCheckedItems('categories').length" ng-click="addToMenu('categories')" type="button" class="btn btn-sm btn-add"><?= AdminModule::t('Add To Menu')?></button>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" ng-click="toggle('links')">
                    <?= AdminModule::t('Custom Link')?>
                    <i class="material-icons" ng-hide="collapseExpaned.links">arrow_drop_down</i>
                    <i class="material-icons" ng-show="collapseExpaned.links">arrow_drop_up</i>
                </div>
                <div class="card-block animate-show-hide" ng-show="collapseExpaned.links">
                    <div class="form-group">
                        <label>Url</label>
                        <input type="text" ng-model="data.links.url" value="" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Link text</label>
                        <input type="text" ng-model="data.links.name" value="" class="form-control">
                    </div>
                    <div class="form-group text-right">
                        <button ng-disabled="!data.menu.id || !data.links.url || !data.links.name" ng-click="addToMenu('links')" type="button" class="btn btn-sm btn-add"><?= AdminModule::t('Add To Menu')?></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="luya-content" ng-show="!loadingContent">
            <div class="menu-name row form-group">
                <div class="col-lg-7">
                    <span><?= AdminModule::t('Menu Name:')?></span>
                    <input type="text" ng-model="data.menu.name"> 
                </div>
                <div class="col-lg-5 text-right">
                    <button class="btn btn-sm btn-success" ng-hide="data.menu.id > 0" ng-click="saveMenu()"><?= AdminModule::t('Create Menu')?></button>
                    <button class="btn btn-sm btn-success" ng-show="data.menu.id > 0" ng-click="saveMenu()"><?= AdminModule::t('Save Menu')?></button>
                </div>
            </div>
            <div class="row alert alert-dark form-group" role="alert" ng-show="!data.menu.id">
                <?= AdminModule::t('Give your menu a name, then click Create Menu.')?>
            </div>
            <div ng-hide="!data.menu.id" class="form-group">
                <h3><?= AdminModule::t('Menu Structure')?></h3>
                <div class="row alert alert-dark" role="alert" ng-show="!data.menu.items.length">
                    <?= AdminModule::t('Add menu items from the column on the left.')?>
                </div>
                <ul class="menu-items">
                    <li ng-repeat="item in data.menu.items | filter:{action:'!remove'}"
                        drag-menu dnd-model="item"
                        dnd-ondrop="dropItem(dragged,dropped,position, align,element)"
                        dnd-isvalid="isValid(hover,dragged)"
                        dnd-css="{onDrag: 'drag-start', onHover: 'red', onHoverTop: 'red-top', onHoverMiddle: 'red-middle', onHoverBottom: 'red-bottom'}" 
                        class="menu-item level{{item.level}}"
                    >
                        <div class="card">
                            <div class="card-header" ng-click="item.expanded=!item.expanded">
                                {{ item.name }}
                                <i class="material-icons" ng-hide="item.expanded">arrow_drop_down</i>
                                <i class="material-icons" ng-show="item.expanded">arrow_drop_up</i>
                            </div>
                            <div class="card-block animate-show-hide" ng-show="item.expanded">
                                <div class="form-group">
                                    <label>Url</label>
                                    <input type="text" ng-model="item.url" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Label</label>
                                    <input type="text" ng-model="item.name" class="form-control">
                                </div>
                                <div class="form-group text-right">
                                    <button ng-click="remove(item)" type="button" class="btn btn-link"><?= AdminModule::t('remove')?></button>
                                    <button ng-click="cancel(item)" type="button" class="btn btn-link"><?= AdminModule::t('cancel')?></button>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="clearfix form-group">
                <h3><?= AdminModule::t('Menu Settings')?></h3>
                <p>Display location</p>
                <ul>
                    <li ng-repeat="item in data.menu.positions">
                        <div class="form-check" ng-class="{'form-check-active': item.checked}">
                            <input id="chk-position-{{item.id}}" type="checkbox" ng-click="item.checked=!item.checked" ng-checked="item.checked" class="form-check-input">
                            <label for="chk-position-{{item.id}}">{{item.name}}</label>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-lg-7">
                    <button class="btn btn-sm btn-danger" ng-confirm-click="<?= AdminModule::t('Are you sure to delete this menu ?')?>" confirmed-click="deleteMenu()"><?= AdminModule::t('Delete')?></button>
                </div>
                <div class="col-lg-5 text-right">
                    <button class="btn btn-sm btn-success" ng-hide="data.menu.id > 0" ng-click="saveMenu()"><?= AdminModule::t('Create Menu')?></button>
                    <button class="btn btn-sm btn-success" ng-show="data.menu.id > 0" ng-click="saveMenu()"><?= AdminModule::t('Save Menu')?></button>
                </div>
            </div>

            <div class="overlay" id="menu-overlay" ng-show="processing"></div>
        </div>
    </div>
</div>