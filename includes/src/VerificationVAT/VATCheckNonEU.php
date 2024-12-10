<?php

declare(strict_types=1);

namespace JTL\VerificationVAT;

/**
 * Class VATCheckNonEU
 * @package JTL\VerificationVAT
 */
class VATCheckNonEU extends AbstractVATCheck
{
    /**
     * parse the non-EU string by convention
     *
     * return a array of check-results
     * [
     *        success   : boolean, "true" = all checks were fine, "false" somthing went wrong
     *      , errortype : string, which type of error was occure, time- or parse-error
     *      , errorcode : int, numerical code to identify the error
     *      , errorinfo : additional information to show it the user in the frontend
     * ]
     *
     * @param string $ustID
     * @return array{success: bool, errortype: string, errorcode: int, errorinfo: string}
     */
    public function doCheckID(string $ustID): array
    {
        $VatParser = new VATCheckVatParserNonEU($this->condenseSpaces($ustID));
        if ($VatParser->parseVatId() === true) {
            return [
                'success'   => true,
                'errortype' => 'parse',
                'errorcode' => 0,
                'errorinfo' => ''
            ];
        }

        return [
            'success'   => false,
            'errortype' => 'parse',
            'errorcode' => VATCheckInterface::ERR_PATTERN_MISMATCH,
            'errorinfo' => ($szErrorInfo = $VatParser->getErrorInfo()) !== '' ? (string)$szErrorInfo : ''
        ];
    }
}
