<?php

function validate(array $validations)
{
    $result = [];
    $param = '';
    foreach ($validations as $field => $validate) {
        if (!str_contains($validate, '|')) {
            if (str_contains($validate, ':')) {
                [$validate, $param] = explode(':', $validate);
            }
            $result[$field] = $validate($field, $param);
        } else {
            $result[$field] = multipleValidations($validate, $field, $param);
        }
    }


    if (in_array(false, $result)) {
        return false;
    }

    return $result;
}

function multipleValidations($validate, $field, $param)
{
    $explodePipeValidate = explode('|', $validate);
    foreach ($explodePipeValidate as $validate) {
        if (str_contains($validate, ':')) {
            [$validate, $param] = explode(':', $validate);
        }
        $result = $validate($field, $param);
    }
    return $result;
}

function required($field)
{
    if ($_POST[$field] === '') {
        setFlash($field, 'O campo é obrigatório');
        return false;
    }

    return filter_input(INPUT_POST, $field, FILTER_SANITIZE_STRING);
}

function email($field)
{
    $emailIsValid = filter_input(INPUT_POST, $field, FILTER_VALIDATE_EMAIL);

    if ($_POST[$field] === '') {
        setFlash($field, 'O campo é obrigatório');
        return false;
    } else {
        if (!$emailIsValid) {
            setFlash($field, "O campo tem que ser um email válido");
            return false;
        }
    }
    return filter_input(INPUT_POST, $field, FILTER_SANITIZE_STRING);
}
function unique($field, $param)
{
    $data = filter_input(INPUT_POST, $field, FILTER_SANITIZE_STRING);
    $user = findBy($param, $field, $data);

    if ($user) {
        setFlash($field, "Esse valor já está cadastro");
        return false;
    }

    return $data;
}
function maxlen($field, $param)
{
    $data = filter_input(INPUT_POST, $field, FILTER_SANITIZE_STRING);

    if (strlen($data) > $param) {
        setFlash($field, "Esse campo não pode passar de {$param} caracteres");
        return ($data);
    }
}