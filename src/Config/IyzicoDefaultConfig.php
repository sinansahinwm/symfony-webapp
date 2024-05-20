<?php namespace App\Config;

class IyzicoDefaultConfig
{
    // Default Address Data
    const DEFAULT_USER_ADDRESS_CITY = "Ankara";
    const DEFAULT_USER_ADDRESS_COUNTRY = "Turkey";
    const DEFAULT_USER_ADDRESS_ADDRESS = "Cevizlidere Mah. 1231. Sk. 1/17 Çankaya/ANKARA";
    const DEFAULT_USER_ADDRESS_ZIPCODE = "06520";

    // Default User Identity Data
    const DEFAULT_USER_FULLNAME_NAME = "John";
    const DEFAULT_USER_FULLNAME_LASTNAME = "Doe";
    const DEFAULT_USER_PHONE = "+905444444444";
    const DEFAULT_USER_IDENTITY_NUMBER = "11111111111";

    // Default User Billing & Account Data
    const DEFAULT_USER_LAST_LOGIN_DATE = "2024-10-05 12:43:35";
    const DEFAULT_USER_REGISTRATION_DATE = "2024-04-21 15:12:09";
    const DEFAULT_USER_REGISTRATION_ADDRESS = self::DEFAULT_USER_ADDRESS_ADDRESS;
    const DEFAULT_USER_IP = "85.34.78.112";
    const DEFAULT_USER_CITY = self::DEFAULT_USER_ADDRESS_CITY;
    const DEFAULT_USER_COUNTRY = self::DEFAULT_USER_ADDRESS_COUNTRY;
    const DEFAULT_USER_ZIPCODE = self::DEFAULT_USER_ADDRESS_ZIPCODE;

}