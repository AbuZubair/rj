$(document).ready(function () {
    // Handle avatar upload
    let readURL = function (input) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();

            reader.onload = function (e) {
                $(".profile-pic").attr("src", e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    };

    $(".file-upload").on("change", function () {
        readURL(this);
    });

    $(".upload-button").on("click", function () {
        $(".file-upload").click();
    });
    ////////////////////////////////////////

    $.fn.dataTable.ext.errMode = "throw";

    $("input[data-type='currency']").on({
        keyup: function () {
            formatingCurrency($(this));
        },
    });

    $(".form-register").on("submit", function () {
        $("#register-btn").prop("disable", true);
        $("#register-btn").html("");
        var html = "";
        html += "Loading..";
        $("#register-btn").html(html);
    });

    $("#btn-vedit").click(function (e) {
        e.preventDefault();
        var form = $(".form-admin");
        form.each(function () {
            var input = $(this).find(":input");
            input.each(function () {
                var that = this;
                if ($(that).attr("id")) {
                    $(that).prop("disabled", false);
                }
                if ($(that).attr("type") == "submit") $(that).show();
            });
            $("#btn-vedit").hide();
        });
    });

    $(".parent-list").click(function (e) {
        e.preventDefault();
        let el = e.currentTarget;
        let dropdown = $(el).next();
        $(dropdown).toggle();
        if ($(el).hasClass("opened")) $(el).removeClass("opened");
        else $(el).addClass("opened");
    });

    $(".form-admin").submit(function (e) {
        e.preventDefault();

        var post_url = $(this).attr("action");
        var request_method = $(this).attr("method");
        var form_data = $(this).serialize();
        var redirect = $(this).attr("data-redirect");
        var wording =
            $(this).attr("data-wording") != undefined
                ? $(this).attr("data-wording")
                : "";
        var data_wording;
        if (wording != "") {
            data_wording = {
                id: $("input[name=id]").val(),
                wording: CKEDITOR.instances["konten"].getData(),
            };

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });
        }

        $.ajax({
            url: post_url,
            type: request_method,
            data: wording != "" ? data_wording : form_data,
            beforeSend: function () {
                showNotification("Loading..", "warning", 0);
            },
            success: function (data) {
                var jsonResponse = JSON.parse(data);
                if (jsonResponse.status === 200) {
                    $.notifyClose();
                    showNotification(jsonResponse.message, "success");
                    if (redirect.includes("http")) {
                        setTimeout(function () {
                            window.location.replace(redirect);
                        }, 1000);
                    } else {
                        window[redirect]();
                    }
                } else {
                    $.notifyClose();
                    if (
                        typeof jsonResponse.message === "object" &&
                        jsonResponse.message.constructor === Object
                    ) {
                        var html = "";
                        var msg =
                            jsonResponse.message[
                                Object.keys(jsonResponse.message)[0]
                            ];
                        msg.forEach((element) => {
                            html += element + "<br>";
                        });
                        showNotification(html, "danger", 0);
                        resetList();
                    } else {
                        showNotification(jsonResponse.message, "danger", 0);
                    }
                }
            },
            error: function (xhr) {
                // if error occured
                $.notifyClose();
                if (xhr.responseJSON.errors) {
                    var html = "";
                    var err = xhr.responseJSON.errors;
                    for (var key in err) {
                        if (err.hasOwnProperty(key)) {
                            html += err[key] + "<br>";
                        }
                    }
                    showNotification(html, "danger");
                } else {
                    var msg = xhr.responseJSON.message;
                    showNotification(msg, "danger");
                }
            },
        });
    });

    $(".modal-transaction").on("hidden.bs.modal", function (e) {
        resetList();
    });

    generateBreadcrumbs();
});

function generateBreadcrumbs() {
    const here = location.href.split("/").slice(3);
    let html = '<span>Home</span><span class="breadcrumbs-arrow"></span>';

    for (var i = 0; i < here.length; i++) {
        var part = here[i];
        var text = part;
        if (i > 0) html += '<span class="breadcrumbs-arrow"></span>';
        html += `<span>${capitalizeFirstLetter(
            text.replace(/-/g, " ")
        )}</span>`;
    }
    $("#breadcrumbs").append(html);
}

function capitalizeFirstLetter(string) {
    const arr = string.split(" ");
    for (var i = 0; i < arr.length; i++) {
        arr[i] = arr[i].charAt(0).toUpperCase() + arr[i].slice(1);
    }
    return arr.join(" ");
}

function deleteData(url, id) {
    var $button = $(this);
    var table = $(".dataTable").DataTable();
    $.confirm({
        title: "Konfirmasi!",
        content: "Yakin hapus data?",
        buttons: {
            confirm: function () {
                $.ajax({
                    url: url,
                    type: "GET",
                    data: { id: id },
                    beforeSend: function () {
                        showNotification("Loading..", "warning", 0);
                    },
                    success: function (data) {
                        var jsonResponse = JSON.parse(data);
                        if (jsonResponse.status === 200) {
                            $.notifyClose();
                            showNotification(jsonResponse.message, "success");
                            table.row($button.parents("tr")).remove().draw();
                        } else {
                            showNotification(jsonResponse.message, "danger");
                        }
                    },
                    error: function (xhr) {
                        // if error occured
                        var msg = xhr.responseJSON.message;
                        showNotification(msg, "danger");
                    },
                });
            },
            cancel: function () {
                return;
            },
        },
    });
}

function deleteRow() {
    var url = $("#dynamic-table").attr("delete-url");
    var arr = [];
    var table = $("#dynamic-table").DataTable();
    var rowcollection = table.$("input[type=checkbox]", { page: "all" });
    rowcollection.each(function (index, elem) {
        if ($(elem).prop("checked")) {
            var checkbox_value = $(elem).val();
            arr.push(checkbox_value);
        }
    });
    if (arr.length == 0) {
        showNotification("Silahkan pilih salah satu", "danger");
    } else {
        $.confirm({
            title: "Confirmation!",
            content: "Are you sure??",
            buttons: {
                confirm: function () {
                    $.ajaxSetup({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                    });
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: { data: arr },
                        beforeSend: function () {
                            showNotification("Loading..", "warning", 0);
                        },
                        success: function (data) {
                            var jsonResponse = JSON.parse(data);
                            if (jsonResponse.status === 200) {
                                $.notifyClose();
                                showNotification(
                                    jsonResponse.message,
                                    "success"
                                );
                                table.ajax.reload();
                            } else {
                                showNotification(
                                    jsonResponse.message,
                                    "danger"
                                );
                            }
                        },
                        error: function (xhr) {
                            // if error occured
                            var msg = xhr.responseJSON.message;
                            showNotification(msg, "danger");
                        },
                    });
                },
                cancel: function () {
                    return;
                },
            },
        });
    }
}

function toggle(source) {
    var url = $("#dynamic-table").attr("data-checkbox");
    checkboxes = document.getElementsByName(url);
    for (var i = 0, n = checkboxes.length; i < n; i++) {
        if (!$(checkboxes[i]).prop("disabled"))
            checkboxes[i].checked = source.checked;
    }
}

function singleToggle() {
    document.getElementById("selectAll").checked = false;
}

function add() {
    $(".form-admin").trigger("reset");
    $(".form-admin select").val("").trigger("change");
    var input = $(".form-admin").find(":input");
    input.each(function () {
        var that = this;
        $(that).prop("readonly", false);
        $(that).prop("disabled", false);
    });
    $("input[name=id]").val("");
    var modal = $("#dynamic-table").attr("data-modal");
    $(".modal-title").html("Add");
    $("#" + modal).modal({
        focus: true,
        backdrop: "static",
    });
}

function edit(id, req = null) {
    var url = $("#dynamic-table").attr("edit-url");
    var modal = $("#dynamic-table").attr("data-modal");
    var form = $(".form-admin");
    $.ajax({
        url: url,
        type: "GET",
        data: { id: id },
        success: function (data) {
            var jsonResponse = JSON.parse(data);
            if (jsonResponse.status === 200) {
                form.each(function () {
                    var input = $(this).find(":input");
                    input.each(function () {
                        var that = this;
                        if (
                            $(that).attr("id") &&
                            !$(that).attr("id").includes("btn-vedit")
                        ) {
                            if (req != null) {
                                $(that).prop("disabled", true);
                            } else {
                                $(that).prop("disabled", false);
                            }
                            let val;
                            if ($(that).attr("data-type")) {
                                val = formatCurrency(
                                    jsonResponse.data[$(that).attr("id")]
                                );
                            } else {
                                if (
                                    $(that).attr("id") == "transDate" &&
                                    !jsonResponse.data[$(that).attr("id")]
                                ) {
                                    val =
                                        jsonResponse.data.trans_year +
                                        "-" +
                                        jsonResponse.data.trans_month +
                                        "-" +
                                        jsonResponse.data.trans_date;
                                } else {
                                    val = jsonResponse.data[$(that).attr("id")];
                                }
                            }
                            // let val = ($(that).attr('data-type'))?formatting(jsonResponse.data[$(that).attr('id')]):jsonResponse.data[$(that).attr('id')]
                            if ($(that).attr("type") == "file") {
                                let img = $(`.${$(that).attr("name")}-img`);
                                if (val && val != "") {
                                    const baseUrl = window.location.origin;
                                    img.attr(
                                        "src",
                                        `${baseUrl}/avatars/${val}`
                                    );
                                } else {
                                    img.attr("src", ``);
                                }
                            } else {
                                $(that).val(val);
                            }
                            if ($(that).is(":radio")) {
                                $(that).val(
                                    jsonResponse.data[$(that).attr("name")]
                                );
                                if (
                                    jsonResponse.data[
                                        $(that).attr("name")
                                    ].toString() === $(that).attr("data-value")
                                ) {
                                    $(that).prop("checked", true);
                                } else {
                                    $(that).prop("checked", false);
                                }
                            }
                            if ($(that).hasClass("select2-hidden-accessible")) {
                                let _val =
                                    jsonResponse.data[$(that).attr("id")];
                                if ($(that).hasClass("ajax-remote")) {
                                    // Get attribute data-name
                                    let dataName = $(that).attr("data-name");
                                    let splitDataName = dataName.split("_");
                                    let obj =
                                        jsonResponse.data[splitDataName[0]];
                                    console.log(obj);
                                    // Append option to select2 and add selected attribute
                                    var option = new Option(
                                        obj[splitDataName[1]],
                                        _val,
                                        true,
                                        true
                                    );
                                    $(that).append(option).trigger("change");
                                    // Get span by class name and attribute
                                    setTimeout(() => {
                                        $(
                                            ".select2-selection__rendered[title='" +
                                                obj[splitDataName[1]] +
                                                "']"
                                        ).text(obj[splitDataName[1]]);
                                    });
                                    $(that).trigger({
                                        type: "select2:select",
                                        params: {
                                            data: obj,
                                        },
                                    });
                                } else {
                                    let val =
                                        _val && _val.toString().includes(",")
                                            ? _val.split(",")
                                            : _val;
                                    $(that).val(val).trigger("change");
                                }
                            }
                            if ($(that).hasClass("prevent-edit")) {
                                $(that).prop("readonly", true);

                                if (
                                    $(that).hasClass(
                                        "select2-hidden-accessible"
                                    )
                                ) {
                                    $(that).select2({ disabled: "readonly" });
                                }
                            }
                        }
                        if (req != null && $(that).attr("type") == "submit") {
                            $(that).hide();
                        } else {
                            if (
                                $(that).attr("class") &&
                                $(that).attr("class").includes("btn-ament")
                            ) {
                                $(that).hide();
                            } else {
                                $(that).show();
                            }
                        }
                    });
                    $(".btn-to-edit").hide();
                    $(".modal-title").html("Edit");
                    if (req != null) {
                        $(".btn-to-edit").show();
                        $(".modal-title").html("View");
                    }
                    $("#" + modal).modal();
                });
            } else {
                showNotification(jsonResponse.message, "danger");
            }
        },
        error: function (xhr) {
            // if error occured
            var msg = xhr.responseJSON.message;
            showNotification(msg, "danger");
        },
    });
}

function showNotification(msg, type, delay = "", alwaysOpen = false) {
    let dly = alwaysOpen ? 0 : delay ? delay : 3000;
    $.notify(
        {
            message: msg,
        },
        {
            type: type,
            timer: 1000,
            placement: {
                from: "top",
                align: "right",
            },
            delay: dly,
            z_index: 9999999999,
        }
    );
}

function formatNumber(n) {
    // format number 1000000 to 1,234,567
    return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function formatingCurrency(input, blur) {
    // appends $ to value, validates decimal side
    // and puts cursor back in right position.

    // get input value
    var input_val = input.val();

    // don't validate empty input
    if (input_val === "") {
        return;
    }

    // original length
    var original_len = input_val.length;

    // initial caret position
    var caret_pos = input.prop("selectionStart");

    // check for decimal
    if (input_val.indexOf(",") >= 0) {
        // get position of first decimal
        // this prevents multiple decimals from
        // being entered
        var decimal_pos = input_val.indexOf(",");

        // split number by decimal point
        var left_side = input_val.substring(0, decimal_pos);
        var right_side = input_val.substring(decimal_pos);

        // add commas to left side of number
        left_side = formatNumber(left_side);

        // validate right side
        right_side = formatNumber(right_side);

        // Limit decimal to only 2 digits
        right_side = right_side.substring(0, 2);

        // join number by .
        input_val = left_side + "," + right_side;
    } else {
        // no decimal entered
        // add commas to number
        // remove all non-digits
        input_val = formatNumber(input_val);
        input_val = input_val;
    }

    // send updated string to input
    input.val(input_val);

    // put caret back in the right position
    var updated_len = input_val.length;
    caret_pos = updated_len - original_len + caret_pos;
    input[0].setSelectionRange(caret_pos, caret_pos);
}

function getRole(value) {
    const roles = ["admin", "anggota", "finance", "inventory", "sales"];
    const arrVal = value.split(",");
    const result = arrVal.map((v) => roles[v]);
    return result;
}

function getRandomTemplateColor(val) {
    const templ = {
        admin: "primary",
        anggota: "secondary",
        finance: "success",
        inventory: "info",
        sales: "warning",
    };
    return templ[val];
}

function openDropdwon(e) {
    console.log(e);
    // $(this).parent().css({"color": "red", "border": "2px solid red"})
    // const el = $("ul[attribute="+attr+"]")
    // $("[data-dropdown="+attr+"]").toggle()
}

function formatting(n) {
    // format number 1000000 to 1,234,567
    if (n && n != null && n != "") {
        return n
            .replace(".00", "")
            .replace(/\D/g, "")
            .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    } else {
        return 0;
    }
}

const item_table = $(".item-table tbody");
const input_el = $(".item-table thead").children("tr").first();
let total = 0;
let items = [];

function addItem() {
    const sample_el = item_table.children("tr").first();
    const last_index = item_table.children("tr").last().attr("data-row");
    const idx = parseInt(last_index) + 1;
    let cloned = sample_el.clone();
    let subtotal = 0,
        qty = 0,
        harga = 0,
        disc = 0;
    let selectedItem, discEl;
    let wait = new Promise((resolve, reject) => {
        input_el.children().each(function (index) {
            let el = $(this).children().first();
            let child = cloned.children().eq(index);
            let el_child = child.children().last();
            if (index == 0) child.empty().text(idx + 1);
            if (el.is("input") || el.is("select")) {
                if (!el.val() && !el.attr("id").includes("disc")) {
                    showNotification("Silahkan isi semua field", "danger");
                    reject();
                    return false;
                }

                if (el_child.is("input")) {
                    let id = el_child.attr("id").replace(`-sample`, ``);
                    let name = el_child.attr("name").replace(`-sample`, ``);
                    el_child
                        .attr("name", `${name}[${idx}]`)
                        .attr("id", id + idx)
                        .prop("readonly", true)
                        .val(el.val());
                    if (name.includes("qty"))
                        qty = parseInt(el.val().replaceAll(".", ""));
                    if (name.includes("harga"))
                        harga = parseInt(el.val().replaceAll(".", ""));
                    if (qty != 0 && harga != 0) {
                        if (disc == 0) subtotal = qty * harga;
                    }
                    if (name == "disc") {
                        if (el.val()) {
                            disc = subtotal * (el.val() * 0.01);
                            subtotal = subtotal - disc;
                            el_child.val(
                                disc
                                    .toString()
                                    .replace(".00", "")
                                    .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                            );
                        }
                        discEl = el_child;
                    }
                    if (name.includes("discamt")) {
                        if (el.val() != "") {
                            if (disc != 0)
                                subtotal =
                                    subtotal +
                                    disc -
                                    el.val().replaceAll(".", "");
                            else
                                subtotal =
                                    subtotal - el.val().replaceAll(".", "");
                            discEl.val(el.val());
                        }
                    }
                }
            } else if (el.hasClass("twitter-typeahead")) {
                let input_val = el.children("input").last();
                let name = el_child.attr("name").replace(`-sample`, ``);
                let id = el_child.attr("id").replace(`-sample`, ``);
                if (items.includes(input_val.val())) {
                    showNotification("Item sudah dimasukkan", "danger");
                    reject();
                    return false;
                }
                el_child
                    .attr("name", `${name}[${idx}]`)
                    .attr("id", id + idx)
                    .prop("readonly", true)
                    .val(input_val.val());
                selectedItem = input_val.val();
            } else if (el.children().find("i")) {
                el_child.removeAttr("onclick");
                el_child.children().last().removeAttr("onclick");
                el_child
                    .children()
                    .last()
                    .attr("onClick", "removeItem(" + idx + ")");
            }

            if (el.hasClass("form-check")) {
                const val = el.children().children().first().val();
                let html = `<td class="col-md-1"><input type="hidden" name="is_master_update[${idx}]" value="${val}">${
                    val == 1
                        ? '<span class="badge badge-success">update</span>'
                        : ""
                }</td>`;
                cloned.append(html);
            }

            if ($(this).hasClass("sub-total")) {
                total += subtotal;
                child.text(
                    subtotal
                        .toString()
                        .replace(".00", "")
                        .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                );
            }

            if (index == input_el.children().length - 1) {
                resolve();
            }
        });
    });
    wait.then((res) => {
        items.push(selectedItem);
        cloned.attr("data-row", idx).removeClass("d-none").addClass("d-flex");
        item_table.append(cloned);
        renderTfoot();
        input_el
            .children()
            .find("input[type=checkbox]")
            .prop("checked", false)
            .trigger("change");
        input_el.children().find("input").val("");
        input_el.children().find("select").val("").trigger("change");
    });
}

function removeItem(idx) {
    let subtotal = item_table
        .find(`tr[data-row=${idx}]`)
        .find(".sub-total")
        .text();
    items.splice(idx, 1);
    total = total - parseInt(subtotal.replaceAll(".", ""));
    renderTfoot();
    item_table.find(`tr[data-row=${idx}]`).remove();
    item_table.children().each(function () {
        if (parseInt($(this).attr("data-row")) > parseInt(idx)) {
            let index = parseInt($(this).attr("data-row"));
            $(this)
                .children()
                .each(function (idx) {
                    let that = this;
                    if (idx == 0)
                        $(that)
                            .empty()
                            .text(index - 1 + 1);
                    let el = $(that).children().last();
                    if (el.is("input")) {
                        let name = el
                            .attr("name")
                            .replace(`[${index}]`, `[${index - 1}]`);
                        let id = el
                            .attr("id")
                            .replace(`-${index}`, `-${index - 1}`);
                        el.attr("name", name).attr("id", id);
                    } else {
                        $(that).children().last().removeAttr("onclick");
                        $(that).children().last().off("click");
                        $(that)
                            .children()
                            .last()
                            .on("click", function () {
                                removeItem(index - 1);
                            });
                    }
                });
            $(this).attr("data-row", index - 1);
        }
    });
}

function renderTfoot() {
    if (total > 0) {
        $("tfoot").show();
        $(".total-items").text(
            total
                .toString()
                .replace(".00", "")
                .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        );
    } else {
        $("tfoot").hide();
    }
    $("input[name=charge_amount]").val(total).trigger("change");
}

function editItemTransaction(id, req = null, type = null) {
    var modal = $("#dynamic-table").attr("data-modal");
    var form = $(".form-admin");
    if (req == "view") {
        input_el.children().find("input").attr("disabled", true);
        input_el.children().find("select").attr("disabled", true);
        input_el
            .children()
            .find(".add-btn")
            .removeAttr("onclick")
            .css("opacity", 0.5);
        setTimeout(() => {
            form.find("button[type=submit]").attr("disabled", true);
        }, 500);
        $("textarea[name=note]").attr("disabled", true);
        $(".por-btn").attr("disabled", true);
    } else {
        input_el.children().find("input").attr("disabled", false);
        input_el.children().find("select").attr("disabled", false);
        if (!input_el.children().find(".add-btn").attr("onclick"))
            input_el
                .children()
                .find(".add-btn")
                .attr("onclick", "addItem()")
                .css("opacity", 1);
        form.find("button[type=submit]").attr("disabled", false);
        $("textarea[name=note]").attr("disabled", false);
    }
    renderRow(id, req).then((res) => {
        $(".list-item").show();
        $("#" + modal).modal();
    });
}

function renderRow(id, req = null, type = null) {
    return new Promise((resolve) => {
        var url = $("#dynamic-table").attr("edit-url");
        let param = { transaction_no: id };
        if (type == "por") param.isRemaining = true;
        $.ajax({
            url: url,
            type: "GET",
            data: param,
            success: function (data) {
                var jsonResponse = JSON.parse(data);
                if (jsonResponse.status) {
                    const rows = jsonResponse.data;
                    if (type != "por") {
                        $("input[name=id]").val(rows[0].id);
                        $("input[name=transaction_no]").val(
                            rows[0].transaction_no
                        );
                        $("textarea[name=note]").val(rows[0].note);
                        $("input[name=transDate]").val(
                            rows[0].trans_year +
                                "-" +
                                rows[0].trans_month +
                                "-" +
                                rows[0].trans_date
                        );
                        $(".reference_string").val(rows[0].reference_no);
                        $(".reference_string").show();
                        $(".reference").select2("destroy").hide();
                    }
                    for (let index = 0; index < rows.length; index++) {
                        items.push(
                            `${rows[index].item_name} (code:${rows[index].item_code})`
                        );
                        const subtotal = rows[index].amount;
                        if (type == "por") total += parseInt(subtotal);
                        else total = parseInt(rows[index].charge_amount);
                        const cloned = item_table
                            .children("tr")
                            .first()
                            .clone();
                        cloned.children().each(function (idx) {
                            const el = $(this).children().last();
                            if (idx == 0) el.empty().text(index + 1);
                            if (el.is("input")) {
                                let id = el.attr("id").replace(`-sample`, ``);
                                let name = el
                                    .attr("name")
                                    .replace(`-sample`, ``);
                                let val;
                                if (name.includes("item"))
                                    val = `${rows[index].item_name} (code:${rows[index].item_code})`;
                                else if (name.includes("qty"))
                                    val = rows[index].quantity;
                                else if (name.includes("harga"))
                                    val = rows[index].harga
                                        .toString()
                                        .replace(".00", "")
                                        .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                else if (name.includes("satuan"))
                                    val = rows[index].satuan;
                                else if (name.includes("konversi"))
                                    val = rows[index].konversi
                                        ? rows[index].konversi
                                        : rows[index].konversi_master;
                                el.attr("name", `${name}[${index}]`)
                                    .attr("id", id + index)
                                    .prop("readonly", true)
                                    .val(val);
                            }

                            if (el.children().find("i")) {
                                el.removeAttr("onclick");
                                let btn = el.children().last();
                                btn.removeAttr("onclick");
                                if (req == "view") btn.css("opacity", 0.5);
                                else
                                    btn.attr(
                                        "onClick",
                                        "removeItem(" + index + ")"
                                    );
                            }

                            if ($(this).hasClass("sub-total")) {
                                $(this).text(
                                    subtotal
                                        .toString()
                                        .replace(".00", "")
                                        .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                                );
                            }
                        });
                        cloned
                            .attr("data-row", index)
                            .removeClass("d-none")
                            .addClass("d-flex");
                        item_table.append(cloned);
                        renderTfoot();
                    }
                    resolve();
                } else {
                    showNotification(jsonResponse.message, "danger");
                }
            },
            error: function (xhr) {
                // if error occured
                var msg = xhr.responseJSON.message;
                showNotification(msg, "danger");
            },
        });
    });
}

function resetList() {
    item_table.children().each(function () {
        if ($(this).attr("data-row") && $(this).attr("data-row") > -1) {
            $(this).remove();
        }
    });
    total = 0;
    items = [];
    renderTfoot();
}

function getDate() {
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, "0");
    var mm = String(today.getMonth() + 1).padStart(2, "0"); //January is 0!
    var yyyy = today.getFullYear();

    today =
        mm +
        "-" +
        dd +
        "-" +
        yyyy +
        " " +
        today.getHours() +
        ":" +
        today.getMinutes() +
        ":" +
        today.getSeconds();
    return today;
}

function formatCurrency(data) {
    data = data ? data : 0;
    return data
        .toString()
        .replace(".00", "")
        .replace(".", ",")
        .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function formattingDate(month, year) {
    const monthName = {
        "01": "Januari",
        "02": "Februari",
        "03": "Maret",
        "04": "April",
        "05": "Mei",
        "06": "Juni",
        "07": "Juli",
        "08": "Agustus",
        "09": "September",
        10: "Oktober",
        11: "November",
        12: "Desember",
    };
    let mth = monthName[month];
    return mth + " " + year;
}

function formatYYYYMMDDtoDDMMYYYY(date) {
    return date.split("-").reverse().join("-");
}

function openUploadModal() {
    $("#importModal").modal({
        focus: true,
    });
}

function uploadItem(url, callback) {
    var fileExtension = ["xlsx"];
    var file_data = $("#item-files").prop("files")[0];
    if (!file_data) {
        showNotification("Silahkan pilih file", "danger");
        return;
    }
    if (
        $.inArray(
            $("#item-files").val().split(".").pop().toLowerCase(),
            fileExtension
        ) == -1
    ) {
        showNotification("Format file salah", "danger");
        return;
    }
    var form_data = new FormData();
    form_data.append("file", file_data);
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        url,
        type: "POST",
        data: form_data,
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function () {
            showNotification("Loading..", "warning", 0);
        },
        success: function (data) {
            $.notifyClose();
            var jsonResponse = JSON.parse(data);
            callback(jsonResponse);
        },
        error: function (xhr) {
            // if error occured
            $.notifyClose();
            let msg = xhr.responseJSON.message;
            if(xhr.responseJSON.errors && xhr.responseJSON.errors.length > 0){
                msg = "<ul>";
                $.each(xhr.responseJSON.errors, function(key, val){
                    msg += "<li>" + val + "</li>";
                });
                msg += "</ul>";
            }
            showNotification(msg, "danger", 3000);
        },
    });
}

function formatSiswaSelection(siswa) {
    if (siswa.loading) {
        return siswa.text;
    }

    var $container = $(
        "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__avatar'><img src='" +
            `${window.location.origin}/avatars/${siswa.avatar}` +
            "'  onerror=\"this.onerror=null;this.src='" +
            `${window.location.origin}/images/avatar.png` +
            "'\"/></div>" +
            "<div class='select2-result-repository__meta'>" +
            "<div class='select2-result-repository__title'></div>" +
            "<div class='select2-result-repository__description'></div>" +
            "<div class='select2-result-repository__statistics'>" +
            "<div class='select2-result-repository__jenjang'></div>" +
            "<div class='select2-result-repository__kelas'></div>" +
            "</div>" +
            "</div>" +
            "</div>"
    );

    $container.find(".select2-result-repository__title").text(siswa.fullname);
    $container
        .find(".select2-result-repository__description")
        .text(`${siswa.tempat_lahir}, ${siswa.tanggal_lahir}`);
    $container
        .find(".select2-result-repository__jenjang")
        .append(`Jenjang: ${siswa.jenjang}`);
    $container
        .find(".select2-result-repository__kelas")
        .append(`Kelas: ${siswa.kelas}`);

    return $container;
}

function formatSiswaValueSelection(siswa) {
    return siswa.fullname;
}
