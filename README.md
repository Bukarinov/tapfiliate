# Tapfiliate Coding Exercise

## Description
Users visit the Shop site. They can come from search engines (organic traffic), come from partner links of several cashback services: Ours and others (Theirs1, Theirs2).

Examples of JSON logs in the Ours service DB, which are collected in script from all pages of the store website:

1) Organic transition of the customer to the store
```json
{
  "client_id": "user15",
  "User-Agent": "Firefox 59",
  "document.location": "https://shop.com/products/?id=2",
  "document.referer": "https://yandex.ru/search/?q=купить+котика",
  "date": "2018-04-03T07:59:13.286000Z"
}
```
2) The customer goes to the store via the cashback service partner link
```json
{
  "client_id": "user15",
  "User-Agent": "Firefox 59",
  "document.location": "https://shop.com/products/?id=2",
  "document.referer": "https://referal.ours.com/?ref=123hexcode",
  "date": "2018-04-04T08:30:14.104000Z"
},
{
  "client_id": "user15",
  "User-Agent": "Firefox 59",
  "document.location": "https://shop.com/products/?id=2",
  "document.referer": "https://ad.theirs1.com/?src=q1w2e3r4",
  "date": "2018-04-04T08:45:14.384000Z"
}
```

3) Internal Customer Transition in Store
```json
{
  "client_id": "user15",
  "User-Agent": "Firefox 59",
  "document.location": "https://shop.com/checkout",
  "document.referer": "https://shop.com/products/?id=2",
  "date": "2018-04-04T08:59:16.222000Z"
}
```

Shop pays cashback services for customers who click on their link, paid for the goods and at the end hit the https://shop.com/checkout page ("Thank you for ordering"). The commission is paid on the principle of "wins the last cashback service, after clicking on the partner link of which the client bought the goods." Organic transitions do not change the attribution of the cashback service order.

The Ours service wants to find customers who made the purchase thanks to it by their logs. You need to write a program that looks for winning partner links to the Ours service. Consider different scenarios of client behavior on the site.

In the result, the manager expects to receive a list of customers and what partner links led to their purchases.
