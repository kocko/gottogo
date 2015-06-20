<?php
    include_once 'head.php';
?>
<script>
    jQuery(function () {
        jQuery.getJSON('../utils/cities_and_countries.php').done(function (data) {
            var sourceArray = [];
            for (var i = 0; i < data.length; i++) {
                sourceArray.push(data[i]['city'] + ", " + data[i]['country']);
            }
            jQuery('input').typeahead({source: sourceArray});
        });
    });

    function disableDestinationEnableOrganizer() {
        jQuery("#destination").prop('readonly', true);
        jQuery("#organizer").show();
    }

    function disableCountsEnableBudget() {
        jQuery("#nightsCountErrorMessage").hide();
        jQuery("#touristsCountErrorMessage").hide();
        var nightsCount = jQuery("#nightsCount").val();
        var touristsCount = jQuery("#touristsCount").val();
        var error = false;
        if (nightsCount == '') {
            jQuery("#nightsCountErrorMessage").show();
            error = true;
        }
        if (touristsCount == '') {
            jQuery("#touristsCountErrorMessage").show();
            error = true
        }
        if (error) {
            return;
        }
        jQuery("#nightsCountErrorMessage").hide();
        jQuery("#touristsCountErrorMessage").hide();
        jQuery("#budgetCountSave").prop('disabled', false);
        jQuery("#touristsCount").prop('readonly', true);
        jQuery("#nightsCount").prop('readonly', true);
        jQuery("#budgetOrganizer").show();
    }

    function validateBudgetCost(obj) {
        if (!obj.checkValidity()) {
            obj.focus();
            //todo add custom validity
        }
    }

    function addLuggageItem(category) {
        var addMoreLuggageItemsDiv = jQuery("#addMoreLuggageItems_" + category);
        jQuery(addMoreLuggageItemsDiv).append(
            '<div>' +
            '   <input class="form-control" type="text" name="' + category + '">' +
            '   <button type="button" class="btn btn-danger btn-xs id="removeLuggageItemsButton_' + category + '" title="Премахни" onclick="removePlanItem(this)">' +
            '       <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>' +
            '   </button><br />' +
            '</div>'
        );
    }

    function addBudgetItem(category) {
        var addMoreLuggageItemsDiv = jQuery("#addMoreBudgetItems_" + category);
        jQuery(addMoreLuggageItemsDiv).append(
            '<div>' +
            '   <input class="form-control" type="text" name="' + category + '" placeholder="Заглавие">' +
            '   <input class="form-control" type="text" name="budget_' + category + '" placeholder="Стойност" pattern="^\d+([.,]\d+)?$">' +
            '   <button type="button" class="btn btn-danger btn-xs id="removeBudgetItemsButton_' + category + '" title="Премахни" onclick="removePlanItem(this)">' +
            '       <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>' +
            '   </button><br />' +
            '</div>'
        );
    }

    function removePlanItem(component) {
        jQuery(component).parent('div').remove();
    }

    function validateDestination() {
        //todo
        if (jQuery("#destination").val() == "") {
            jQuery("#destination").validate('validate');
            return true;
        } else {
            return false;
        }
    }

    jQuery(function(){
        jQuery("#new_trip_action").click(function() {
            var destination = jQuery('#destination').val();
            var luggage_items = jQuery(":checkbox").serializeArray();

            var selected_luggage_items = [];
            for (var i = 0; i < luggage_items.length; i++) {
                selected_luggage_items.push([luggage_items[i].name, luggage_items[i].value]);
            }

            var all_items = jQuery(":text");
            var budget_items = [];
            for (var i = 0; i < all_items.length; i++) {
                if (all_items[i].name != 'destination' && all_items[i].name.lastIndexOf('budget_') != 0) {
                    selected_luggage_items.push([all_items[i].name, all_items[i].value]);
                } else if (all_items[i].name.lastIndexOf('budget_') === 0 && all_items[i].value != '') {
                    var split = all_items[i].name.split("_");
                    var name = split[1];
                    var shared = split[2];
                    var category = split[3];
                    var cost = all_items[i].value;
                    budget_items.push([name, cost, category, shared]);
                }
            }

            var touristsCount = jQuery("#touristsCount").val();
            var nightsCount = jQuery("#nightsCount").val();

            jQuery.post("<?= get_site_url(); ?>/wp-content/plugins/gottogo/views/site/create_new_trip_action.php",
                {
                    destination : destination, selectedLuggageItems: selected_luggage_items,
                    touristsCount: touristsCount, nightsCount : nightsCount,
                    budgetItems : budget_items
                },
                function (result) {
                    if (result == true) {
                        jQuery("#tripSuccessMessage").show(400);
                        jQuery("#newTripDataForm").hide();
                    } else {
                        jQuery("#tripErrorMessage").show(400);
                    }
                }
            );
        });
    });
</script>
<div class="row in" id="newtrip" aria-expanded="true" aria-controls="newTripCollapse">
    <div class="col-xs-12">
        <div class="well">
            <h1>Избор на пътуване</h1>
            <div>
                Вие сте избрали да създадете ново пътуване. За тази цел първо трябва да въведете дестинация в съответното поле и да натиснете бутон 'Запази'.
                Направихте ли го? Чудесно!
                След това системата Ви предоставя възможност за избор между два вида планиране - на бюджет и на багаж. Вие трябва да изберете да планирате първо едното, а след това (ако желаете) и другото.
                Важно нещо, което трябва да запомните, е че когато натиснете бутон 'Създай', Вие сте приключили процеса на планиране и може да намерите Вашето ново пътуване в страницата Моите пътувания. Там са предложени няколко операции за действие, които може да са Ви полезни.
                А сега успех!
            </div>
            <br />
            <div class="alert alert-info collapse alert-dismissible" id="tripSuccessMessage">
                Пътуването е създадено успешно! Може да го видите на страницата
                <a href="mytrips.php" type="button" class="btn btn-success btn-sm">
                    Моите пътувания
                </a>
            </div>
            <div class="alert alert-info collapse alert-dismissible" id="tripErrorMessage">
                Възникна грешка! Моля, опитайте отново!
            </div>
            <form class="form-inline" action="" method="post" id="newTripDataForm">
                <div class="row">
                    <div class="form-group">
                        <input class="typeahead form-control" type="text" autocomplete="off"
                               placeholder="Въведете дестинация: City, Country"
                               id="destination" name="destination" value="">
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-info"
                                onclick="disableDestinationEnableOrganizer()">Запази</button>
                    </div>
                </div>
                <div class="row" style="height: 15pt;"></div>
                <div id="organizer" style="display: none;">
                    <div class="row">
                        <div class="col-xs-4">
                            <button type="button" class="btn btn-success btn-block" onclick="switchBetweenCollapsibleDivs('organizeBudgetDiv', 'organizeLuggageDiv')">Планиране на бюджет</button>
                        </div>
                        <div class="col-xs-4">
                            <button type="button" class="btn btn-success btn-block" onclick="switchBetweenCollapsibleDivs('organizeLuggageDiv', 'organizeBudgetDiv')">Организиране на багаж</button>
                        </div>
                        <div class="col-xs-4">
                            <button type="button" class="btn btn-success btn-block" disabled>Планиране на маршрут </button>
                        </div>
                    </div>
                    <div class="row" style="height: 15pt;"></div>
                    <div class="row collapse" id="organizeBudgetDiv" aria-expanded="false" aria-controls="organizeBudgetCollapse">
                        <?php organizeBudget(); ?>
                    </div>
                    <div class="row collapse" id="organizeLuggageDiv" aria-expanded="false">
                        <?php organizeLuggage(); ?>
                    </div>
                    <div class="row" style="height: 15pt;"></div>
                </div>
                <div class="row" style="height: 15pt;"></div>
                <div class="row" id="createNewTripArea">
                    <a type="submit" class="btn btn-danger btn-block"
                       id="new_trip_action" name="new_trip_action">Създай</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
    function getLuggageItemsNavigationTabs() {
        require_once '../utils/luggage_utils.php';
?>
    <ul class="nav nav-tabs" role="tablist">
    <?php
        $categories = getLuggageItemsCategories();
        foreach ($categories as $category) {
            ?><li role="presentation"><a href="#<?= join("_", explode(" ", mb_strtolower($category, "UTF-8"))); ?>" aria-controls="home" role="tab" data-toggle="tab"><?= $category; ?></a></li><?php
        }
    ?>
    </ul>
<?php
    }
?>
<?php
    function getLuggageTabPanels() {
?>
    <div class="tab-content">
    <?php
        $categories = getLuggageItemsCategories();
        foreach ($categories as $category) {
            ?>
            <div role="tabpanel" class="tab-pane" id="<?= join("_", explode(" ", mb_strtolower($category, "UTF-8"))); ?>">
            <?php
                $items = getLuggageItemsPerCategory($category);
                foreach ($items as $item) {
                    ?>
                    <div class="checkbox checkbox-success">
                        <input id="<?= $item; ?>" type="checkbox" name="<?= $category; ?>" value="<?= $item; ?>">
                        <label for="<?= $item; ?>"><?= $item; ?></label>
                    </div>
                    <?php
                }
            ?>
                <div id="addMoreLuggageItems_<?= join("_", explode(" ", mb_strtolower($category, "UTF-8"))); ?>"></div>
                <button type="button" class="btn btn-info btn-xs" id="addMoreLuggageItemsButton_<?= join("_", explode(" ", mb_strtolower($category, "UTF-8"))); ?>"
                        title="Добави" onclick="addLuggageItem('<?= join("_", explode(" ", mb_strtolower($category, "UTF-8"))); ?>')">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                </button>
            </div>
        <?php
        }
        ?>
    </div>
    <?php
    }

    function getBudgetNavigationTabs() {
        require_once '../utils/budget_utils.php';
    ?>
        <div id="budgetOrganizer" style="display:none;">
            <ul class="nav nav-tabs" role="tablist">
                <?php
                $categories = getBudgetCategories();
                foreach ($categories as $category) {
                    ?><li role="presentation"><a href="#<?= join("_", explode(" ", mb_strtolower($category, "UTF-8"))); ?>" aria-controls="home" role="tab" data-toggle="tab"><?= $category; ?></a></li><?php
                }
                ?>
            </ul>
        </div>
    <?php
    }

    function getNightsStayingAndPeopleTravellingDiv() {
    ?>

        <input class="form-control" autocomplete="off" type="number"
               placeholder="Брой пътници" id="touristsCount" name="touristsCount" min="1" step="1">
        <input class="form-control" autocomplete="off" type="number"
               placeholder="Брой нощувки" id="nightsCount" name="nightsCount" min="1" step="1">
        <div class="form-group">
            <button type="button" class="btn btn-block btn-info" id="budgetCountSave" onclick="disableCountsEnableBudget()">Запази</button>
        </div>
        <div class="row" style="height: 15pt;"></div>
        <div class="alert alert-info collapse alert-dismissible" id="touristsCountErrorMessage">
            Моля, въведете положителна числова стойност за 'Брой пътници'!
        </div>
        <div class="alert alert-info collapse alert-dismissible" id="nightsCountErrorMessage">
            Моля, въведете положителна числова стойност за 'Брой нощувки'!
        </div>
        <div class="row" style="height: 15pt;"></div>
    <?php
    }

    function getBudgetTabPanels() {
    ?>
        <div class="tab-content">
            <?php
            $categories = getBudgetCategories();
            foreach ($categories as $category) {
                ?>
                <div role="tabpanel" class="tab-pane" id="<?= join("_", explode(" ", mb_strtolower($category, "UTF-8"))); ?>">
                    <?php
                    $items = getBudgetCostsPerCategory($category);
                    foreach ($items as $item) {
                        ?>
                        <label for="<?= $item['name']; ?>"><?= $item['name']; ?></label>
                        <input id="<?= $item['name']; ?>" name="budget_<?= $item['name']; ?>_<?= $item['shared'];?>_<?= $category; ?>"
                               class="form-control" pattern="^\d+([.,]\d+)?$"
                               onblur="validateBudgetCost(this)">
                        <?php
                            echo $item['shared'] == 1 ? '(общо)' : '(на човек)';
                        ?>
                        <br />
                    <?php
                    }
                    ?>
                    <div id="addMoreBudgetItems_<?= join("_", explode(" ", mb_strtolower($category, "UTF-8"))); ?>"></div>
                    <button type="button" class="btn btn-info btn-xs" id="addMoreLuggageItemsButton_<?= join("_", explode(" ", mb_strtolower($category, "UTF-8"))); ?>"
                            title="Добави" onclick="addBudgetItem('<?= join("_", explode(" ", mb_strtolower($category, "UTF-8"))); ?>')">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                    </button>
                </div>
            <?php
            }
            ?>
        </div>
    <?php
    }

    function organizeLuggage() {
        getLuggageItemsNavigationTabs();
        getLuggageTabPanels();
    }

    function organizeBudget() {
        getNightsStayingAndPeopleTravellingDiv();
        getBudgetNavigationTabs();
        getBudgetTabPanels();
    }
