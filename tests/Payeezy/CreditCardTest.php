<?php

class Payeezy_CreditCardTest extends BaseTest
{
  private function getPrimaryTxPayload()
  {
    $args = [
    "merchant_ref" => "Astonishing-Sale",
    "amount" => "1299",
    "currency_code" => "USD",
    "credit_card" => array(
      "type" => "visa",
      "cardholder_name" => "John Smith",
      "card_number" => "4788250000028291",
      "exp_date" => "1020",
      "cvv" => "123"
    ),
    "billing_address" => array(
      "street" => "123 Fake St.",
      "state_province" => "NY",
      "city" => "New York",
      "country" => "USA",
      "email" => "test@phpunit.com",
      "zip_postal_code" => "12345",
      "phone" => array(
        "type" => "Cell",
        "number" => "123-456-7890"
      )
    ),
    "level2" => array(
      "tax1_amount" => "100",
      "tax1_number" => "101",
      "tax2_amount" => "200",
      "tax2_number" => "202",
      "customer_ref" => "7000"
    ),
    "level3" => array(
      "alt_tax_amount" => "100",
      "alt_tax_id" => "1000",
      "discount_amount" => "5",
      "duty_amount" => "6",
      "freight_amount" => "7",
      "ship_from_zip" => "10000",
      "ship_to_address" => array(
        "address_1" => "123 Ship St.",
        "city" => "New York",
        "state" => "NY",
        "zip" => "12345",
        "country" => "USA",
        "email" => "shipto@me.com",
        "phone" => "555-666-7777",
        "name" => "The Customer"
      ),
      "line_items" => array(
          array(
            "description" => "Tacos",
            "quantity" => "10",
            "commodity_code" => "090909",
            "discount_amount" => "69",
            "discount_indicator" => "1",
            "gross_net_indicator" => "0",
            "line_item_total" => "14300",
            "product_code" => "Level3",
            "tax_amount" => "100",
            "tax_rate" => "5",
            "tax_type" => "2",
            "unit_cost" => "1500",
            "unit_of_measure" => "EA",
            "email" => "tac@os.com"
          )
      )
    )
    ];
    
    return $args;
  }

  public function testAuthorize()
  {
    $transaction = new Payeezy_CreditCard(self::$client);
    $response = $transaction->authorize(self::getPrimaryTxPayload());
    $this->assertEquals($response->transaction_status, "approved");
  }
    
  public function testPurchase()
  {
    $transaction = new Payeezy_CreditCard(self::$client);
    $response = $transaction->purchase(self::getPrimaryTxPayload());
    $this->assertEquals($response->transaction_status, "approved");
  }
    
  public function testCapture()
  {
    $authorize_card_transaction = new Payeezy_CreditCard(self::$client);
    $authorize_response = $authorize_card_transaction->authorize(self::getPrimaryTxPayload());
      
    $capture_card_transaction = new Payeezy_CreditCard(self::$client);
    $capture_response = $capture_card_transaction->capture(
        $authorize_response->transaction_id,
        array(
        "amount"=> "1200",
        "transaction_tag" => $authorize_response->transaction_tag,
        "merchant_ref" => "Astonishing-Sale",
        "currency_code" => "USD",
        )
    );
    $this->assertEquals($capture_response->transaction_status, "approved");
  }
}
