<?php

/**
* Chase Paymentech
* Credit Card Transaction
* Authirize and Capture
*/

$xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Request>
<NewOrder >
    <IndustryType>{$IndustryType}</IndustryType>
    <MessageType>{$MessageType}</MessageType>
    <BIN>{$Bin}</BIN>
    <MerchantID>{$MerchantID}</MerchantID>
    <TerminalID>{$TerminalID}</TerminalID>
    <CardBrand>{$CardBrand}</CardBrand>
    <AccountNum>{$CardNumber}</AccountNum>
    <Exp>{$Exp}</Exp>
    <CurrencyCode>{$CurrencyCode}</CurrencyCode>
    <CurrencyExponent>{$CurrencyExponent}</CurrencyExponent>
    <CardSecVal>{$CardSecVal}</CardSecVal>
    <AVSzip>{$AVSzip}</AVSzip>
    <AVSaddress1>{$AVSaddress1}</AVSaddress1>
    <AVSaddress2>{$AVSaddress2}</AVSaddress2>
    <AVScity>{$AVScity}</AVScity>
    <AVSstate>{$AVSstate}</AVSstate>
    <AVSphoneNum>{$AVSphoneNum}</AVSphoneNum>
    <AVSname>{$AVSname}</AVSname>
    <OrderID>{$OrderID}</OrderID>
    <Amount>{$Amount}</Amount>
    <Comments>{$Comments}</Comments>
</NewOrder>
</Request>
XML;

echo $xml;
