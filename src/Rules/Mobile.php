<?php

namespace WenGg\WebmanLaravelBase\Rules;

$version = (int)\Composer\InstalledVersions::getVersion("illuminate/contracts");
if ($version >= 10) {
    class Mobile implements \Illuminate\Contracts\Validation\ValidationRule
    {
        /**
         * Run the validation rule.
         *
         * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
         */
        public function validate(string $attribute, mixed $value, \Closure $fail): void
        {
            if (!(is_string($value) && preg_match('/^1\d{10}$/', $value))) {
                // $fail("The :attribute format error.");
                $fail("validation.mobile")->translate();
            }
        }
    }
} else {
    class Mobile implements \Illuminate\Contracts\Validation\Rule
    {
        public function passes($attribute, $value)
        {
            return is_string($value) && preg_match('/^1\d{10}$/', $value) ? true : false;
        }

        public function message()
        {
            return trans('mobile', [], 'validation');
        }
    }
}
