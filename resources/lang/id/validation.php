<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute harus diterima.',
    'active_url' => ':attribute bukan URL yang valid.',
    'after' => ':attribute harus berupa tanggal setelah tanggal.',
    'after_or_equal' => ':attribute harus berupa tanggal setelah atau sama dengan tanggal.',
    'alpha' => ':attribute hanya dapat berisi huruf.',
    'alpha_dash' => ':attribute hanya dapat berisi huruf, angka, tanda hubung dan garis bawah.',
    'alpha_num' => ':attribute hanya dapat berisi huruf dan angka.',
    'array' => ':attribute harus berupa array.',
    'before' => ':attribute harus tanggal sebelum tanggal.',
    'before_or_equal' => ':attribute harus tanggal sebelum atau sama dengan tanggal.',
    'between' => [
        'numeric' => ':attribute harus antara :min dan :max.',
        'file' => ':attribute harus antara :min dan :max kilobyte.',
        'string' => ':attribute harus antara :min dan karakter maks.',
        'array' => ':attribute harus memiliki antara :min dan item maks.',
    ],
    'boolean' => ':attribute atribut harus benar atau salah.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'date' => ':attribute bukan tanggal yang valid.',
    'date_equals' => ':attribute harus berupa tanggal yang sama dengan tanggal.',
    'date_format' => ':attribute tidak cocok dengan format format.',
    'different' => ':attribute dan lainnya harus berbeda.',
    'digits' => ':attribute harus digit digit.',
    'digits_between' => ':attribute harus antara :min dan digit maks.',
    'dimensions' => ':attribute memiliki dimensi gambar yang tidak valid.',
    'distinct' => ':attribute atribut memiliki :values duplikat.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'ends_with' => ':attribute harus diakhiri dengan salah satu dari berikut ini :values.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'file' => ':attribute harus berupa file.',
    'filled' => ':attribute atribut harus memiliki :values.',
    'gt' => [
        'numeric' => ':attribute harus lebih besar dari :values.',
        'file' => ':attribute harus lebih besar dari value kilobytes.',
        'string' => ':attribute harus lebih besar dari karakter :values.',
        'array' => ':attribute harus memiliki lebih dari item :values.',
    ],
    'gte' => [
        'numeric' => ':attribute harus lebih besar dari atau sama dengan :values.',
        'file' => ':attribute harus lebih besar dari atau sama dengan :values kilobyte.',
        'string' => ':attribute harus lebih besar dari atau sama dengan karakter :values.',
        'array' => ':attribute harus memiliki item :values atau lebih.',
    ],
    'image' => ':attribute harus berupa gambar.',
    'in' => ':attribute yang dipilih tidak valid.',
    'in_array' => ':attribute atribut tidak ada di :other.',
    'integer' => ':attribute harus berupa bilangan bulat.',
    'ip' => ':attribute harus berupa alamat IP yang valid.',
    'ipv4' => ':attribute harus berupa alamat IPv4 yang valid.',
    'ipv6' => ':attribute harus alamat IPv6 yang valid.',
    'json' => ':attribute harus berupa string JSON yang valid.',
    'lt' => [
        'numeric' => ':attribute harus lebih kecil dari :values.',
        'file' => ':attribute harus kurang dari :values kilobyte.',
        'string' => ':attribute harus kurang dari karakter :values.',
        'array' => ':attribute harus memiliki item kurang dari :values.',
    ],
    'lte' => [
        'numeric' => ':attribute harus kurang dari atau sama dengan :values.',
        'file' => ':attribute harus kurang dari atau sama :values kilobyte.',
        'string' => ':attribute harus kurang dari atau sama dengan karakter :values.',
        'array' => ':attribute tidak boleh memiliki lebih dari item :values.',
    ],
    'max' => [
        'numeric' => ':attribute mungkin tidak lebih besar dari :max.',
        'file' => ':attribute tidak boleh lebih besar dari :max kilobyte.',
        'string' => ':attribute mungkin tidak lebih besar dari :max karakter.',
        'array' => ':attribute mungkin tidak memiliki lebih dari item maks.',
    ],
    'mimes' => ':attribute harus berupa file type  :values.',
    'mimetypes' => ':attribute harus berupa file type :values.',
    'min' => [
        'numeric' => ':attribute setidaknya harus :min.',
        'file' => ':attribute setidaknya harus :min kilobyte.',
        'string' => ':attribute setidaknya harus :min karakter.',
        'array' => ':attribute harus memiliki setidaknya :min item.',
    ],
    'not_in' => ':atribut yang dipilih tidak valid.',
    'not_regex' => 'Format :attribute tidak valid.',
    'numeric' => ':attribute harus berupa angka.',
    'password' => 'Kata sandi salah.',
    'present' => ':attribute atribut harus ada.',
    'regex' => 'Format :attribute tidak valid.',
    'required' => 'Bidang :attribute diperlukan.',
    'required_if' => 'Bidang :attribute diperlukan ketika lainnya adalah :values.',
    'required_unless' => 'Bidang :attribute diperlukan kecuali yang lain di :values.',
    'required_with' => 'Bidang :attribute diperlukan ketika :values hadir.',
    'required_with_all' => 'Bidang :attribute diperlukan ketika :values ada.',
    'required_without' => 'Bidang :attribute diperlukan ketika :values tidak ada.',
    'required_without_all' => 'Bidang :attribute diperlukan ketika tidak ada :values hadir.',
    'same' => ':attribute dan lainnya harus cocok.',
    'size' => [
        'numeric' => ':attribute harus :size.',
        'file' => ':attribute harus :size kilobytes.',
        'string' => ':attribute harus karakter :size.',
        'array' => ':attribute harus berisi item :size.',
    ],
    'starts_with' => ':attribute harus dimulai dengan salah satu dari berikut ini :values.',
    'string' => ':attribute harus berupa string.',
    'timezone' => ':attribute harus merupakan zona yang valid.',
    'unique' => ':attribute telah diambil.',
    'uploaded' => ':attribute gagal diunggah.',
    'url' => 'Format :attribute tidak valid.',
    'uuid' => ':attribute harus UUID yang valid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'pesan khusus',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
