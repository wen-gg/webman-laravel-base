<?php

namespace WenGg\WebmanLaravelBase\Rules;

$version = (int)\Composer\InstalledVersions::getVersion("illuminate/contracts");
if ($version >= 10) {
    class Phone implements \Illuminate\Contracts\Validation\ValidationRule
    {
        /**
         * Run the validation rule.
         *
         * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
         */
        public function validate(string $attribute, mixed $value, \Closure $fail): void
        {
            if (!(is_string($value) && preg_match('/^\d{3}-?\d{8}$|^\d{4}-?\d{7}$/', $value))) {
                // $fail("The :attribute format error.");
                $fail("validation.phone")->translate();
            }
        }
    }
} else {
    class Phone implements \Illuminate\Contracts\Validation\Rule
    {
        public function passes($attribute, $value)
        {
            return is_string($value) && preg_match('/^\d{3}-?\d{8}$|^\d{4}-?\d{7}$/', $value) ? true : false;
        }

        public function message()
        {
            return trans('phone', [], 'validation');
        }
    }
}
