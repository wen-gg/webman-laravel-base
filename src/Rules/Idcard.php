<?php

namespace WenGg\WebmanLaravelBase\Rules;

$version = (int)\Composer\InstalledVersions::getVersion("illuminate/contracts");
if ($version >= 10) {
    class Idcard implements \Illuminate\Contracts\Validation\ValidationRule
    {
        /**
         * Run the validation rule.
         *
         * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
         */
        public function validate(string $attribute, mixed $value, \Closure $fail): void
        {
            if (strlen($value) != 18) {
                // $fail("The :attribute must be 18 digits.");
                $fail("validation.idcard_digits")->translate();
            }
            if (!(is_string($value) && preg_match('/^[1-9]\d{5}(?:18|19|20)\d{2}(?:0[1-9]|10|11|12)(?:0[1-9]|[1-2]\d|30|31)\d{3}[\dXx]$/', $value))) {
                // $fail("The :attribute format error.");
                $fail("validation.idcard")->translate();
            }
        }
    }
} else {
    class Idcard implements \Illuminate\Contracts\Validation\Rule
    {
        public function passes($attribute, $value)
        {
            if (strlen($value) != 18) {
                return false;
            }
            return is_string($value) && preg_match('/^[1-9]\d{5}(?:18|19|20)\d{2}(?:0[1-9]|10|11|12)(?:0[1-9]|[1-2]\d|30|31)\d{3}[\dXx]$/', $value) ? true : false;
        }

        public function message()
        {
            return trans('idcard', [], 'validation');
        }
    }
}
