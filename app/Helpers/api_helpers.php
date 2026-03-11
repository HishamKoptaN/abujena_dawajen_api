<?php
function successRes($data = null, $code = 200)
{
    return response()->json(
        $data,
        $code,
    );
}
function failureRes(
    $errors = null,
    $status = 500,
) {
    if (is_array($errors)) {
        $errors = implode(
            ', ',
            $errors,
        );
    }
    return response()->json(
        [
            'error' => $errors,
        ],
        $status,
    );
}
function validationErrorRes($errors, $status = 422)
{
    if (is_array($errors)) {
        $errors = implode(
            ', ',
            $errors,
        );
    }
    return response()->json(
        [
            'error' => $errors,
        ],
        $status,
    );
}
