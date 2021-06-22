function checkField(id) {
    let e = $('#' + id);
    let v = $(e).val();
    let t = $(e).prop('type');
    let p = $(e).closest('.form-group')

    if (v === '' || v === '0' || v === 0) {
        addError(p);
        return false;
    }

    if (t !== 'undefined' && t !== 'select-one') {
        v = v.trim();
    }

    if (t == 'email' && !checkEmail(v)) {
        addError(p);
        return false;
    }

    if (t == 'password' && !сheckPasswordStrength(v)) {
        addError(p);
        return false;
    }

    if (t == 'checkbox' && !checkCheckbox(id)) {
        addError(p);
        return false;
    }

    if (id == 'full-name' && !checkFullName(v)) {
        addError(p);
        return false;
    }

    removeError(p);
    return v;
}

function addError(e) {
    $(e).addClass('input-error');
}

function removeError(e) {
    $(e).removeClass('input-error');
}

function checkRepeat(id, id2) {
    let e = $('#' + id);
    let v = $(e).val();
    let t = $(e).prop('type');
    let p = $(e).closest('.form-group');

    let v2 = $('#' + id2).val();

    if (t !== 'undefined') {
        v = v.trim();
    }

    if (v === '' || v === '0' || v === 0) {
        addError(p)
        return false;
    }

    if (v != v2) {-
        addError(p)
        return false;
    }

    removeError(p);
    return v;
}

function checkEmail(email) {
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

function checkFullName(fullName) {
    const re = /^[a-zA-Z]{2,}(?: [a-zA-Z]+){1}$/;
    return re.test(String(fullName).toLowerCase());
}

function checkCheckbox(id) {
    return $('#' + id).is(":checked");
}

function сheckPasswordStrength(password) {
    let strength = $('#strength');

    // if textBox is empty
    if (password.length == 0){
        $(strength).removeClass();
        return false;
    }

    let cond1 = checkLength(password);
    let cond2 = checkCharAndSpecial(password);
    let cond3 = checkLowerUpper(password);

    let passed = 0;

    if (cond1) passed++;
    if (cond2) passed++;
    if (cond3) passed++;

    // Display of Status
    let newClass = "";
    switch (passed){
        case 0:
            break;
        case 1:
            newClass = "weak";
            break;
        case 2:
            newClass = "good";
            break;
        case 3:
            newClass = "strong";
            break;
    }

    $(strength).removeClass().addClass(newClass);

    if (passed != 3) {
        return false;
    }

    return true;
}

function checkLength(string) {
    return string.length >= 8;
}

function checkCharAndSpecial(string) {
    return hasNumber(string) || hasSpecial(string);
}

function hasNumber(string) {
    return /\d/.test(string);
}

function hasSpecial(string) {
    return /[$@$!%*#?&]/.test(string);
}

function hasChar(string) {
    return /[a-zA-Z]/.test(string);
}

function checkLowerUpper(string) {
    return hasLower(string) && hasUpper(string);
}

function hasLower(string) {
    return /[a-z]/.test(string);
}

function hasUpper(string) {
    return /[A-Z]/.test(string);
}