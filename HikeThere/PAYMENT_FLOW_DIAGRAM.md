# Payment Method Flow Diagram

## Complete System Flow

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         ORGANIZATION SIDE                                │
└─────────────────────────────────────────────────────────────────────────┘

                    ┌──────────────────────────┐
                    │  Payment Setup Page      │
                    │  /org/payment            │
                    └──────────┬───────────────┘
                               │
                               ▼
                    ┌──────────────────────────┐
                    │  TOGGLE SWITCH           │
                    │  ┌────────┐              │
                    │  │ Manual │◄─┐           │
                    │  └────────┘  │ Click     │
                    │      OR      │           │
                    │  ┌──────────┐│           │
                    │  │Automatic ├┘           │
                    │  └──────────┘            │
                    └──────────┬───────────────┘
                               │
                               ▼
              ┌────────────────┴────────────────┐
              │                                  │
              ▼                                  ▼
   ┌──────────────────┐              ┌──────────────────┐
   │  MANUAL PAYMENT  │              │ AUTOMATIC PAYMENT│
   │  Selected        │              │  Selected        │
   └────────┬─────────┘              └────────┬─────────┘
            │                                  │
            ▼                                  ▼
   ┌──────────────────┐              ┌──────────────────┐
   │ Upload QR Code   │              │ Configure Gateway│
   │ Add Instructions │              │ PayMongo/Xendit  │
   └────────┬─────────┘              └────────┬─────────┘
            │                                  │
            ▼                                  ▼
   ┌──────────────────┐              ┌──────────────────┐
   │ Save to Database │              │ Save to Database │
   │ payment_method:  │              │ payment_method:  │
   │    'manual'      │              │   'automatic'    │
   └────────┬─────────┘              └────────┬─────────┘
            │                                  │
            └──────────────┬───────────────────┘
                           │
                           ▼
              ┌─────────────────────────┐
              │  Database Updated       │
              │  Credential Saved       │
              └─────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│                           HIKER SIDE                                     │
└─────────────────────────────────────────────────────────────────────────┘

                    ┌──────────────────────────┐
                    │  Booking Page            │
                    │  Select Trail            │
                    └──────────┬───────────────┘
                               │
                               ▼
                    ┌──────────────────────────┐
                    │  JavaScript Detects      │
                    │  Trail Selection         │
                    └──────────┬───────────────┘
                               │
                               ▼
              ┌────────────────────────────────────┐
              │  API Call:                         │
              │  GET /api/trail/{id}/payment-method│
              └────────────────┬───────────────────┘
                               │
                               ▼
              ┌────────────────────────────────────┐
              │  API Response:                     │
              │  {                                 │
              │    payment_method: 'manual',       │
              │    has_qr_code: true,              │
              │    qr_code_url: '...',             │
              │    payment_instructions: '...'     │
              │  }                                 │
              └────────────────┬───────────────────┘
                               │
              ┌────────────────┴────────────────┐
              │                                  │
              ▼                                  ▼
   ┌──────────────────┐              ┌──────────────────┐
   │ payment_method   │              │ payment_method   │
   │  === 'manual'    │              │  === 'automatic' │
   └────────┬─────────┘              └────────┬─────────┘
            │                                  │
            ▼                                  ▼
   ┌──────────────────┐              ┌──────────────────┐
   │ SHOW:            │              │ SHOW:            │
   │ • QR Code Image  │              │ • Redirect Info  │
   │ • Instructions   │              │ • Gateway Logo   │
   │ • Upload Field   │              │                  │
   │ • Trans. Number  │              │ HIDE:            │
   │                  │              │ • Upload Fields  │
   │ REQUIRE:         │              │ • QR Code        │
   │ ✓ Payment Proof  │              │                  │
   │ ✓ Transaction #  │              │ REQUIRE:         │
   │                  │              │ (Nothing extra)  │
   └────────┬─────────┘              └────────┬─────────┘
            │                                  │
            ▼                                  ▼
   ┌──────────────────┐              ┌──────────────────┐
   │ Hiker Scans QR   │              │ Hiker Clicks     │
   │ Pays via Wallet  │              │ "Book Now"       │
   │ Takes Screenshot │              │                  │
   └────────┬─────────┘              └────────┬─────────┘
            │                                  │
            ▼                                  ▼
   ┌──────────────────┐              ┌──────────────────┐
   │ Uploads Proof    │              │ Redirected to    │
   │ Enters Trans. #  │              │ Payment Gateway  │
   │ Submits Booking  │              │ (PayMongo/Xendit)│
   └────────┬─────────┘              └────────┬─────────┘
            │                                  │
            ▼                                  ▼
   ┌──────────────────┐              ┌──────────────────┐
   │ Booking Created  │              │ Payment Processed│
   │ Status: Pending  │              │ Booking Created  │
   │ Payment: Pending │              │ Status: Confirmed│
   └────────┬─────────┘              └────────┬─────────┘
            │                                  │
            ▼                                  ▼
   ┌──────────────────┐              ┌──────────────────┐
   │ Org Verifies     │              │ Auto-Confirmed   │
   │ Payment Manually │              │ No Verification  │
   └────────┬─────────┘              └────────┬─────────┘
            │                                  │
            ▼                                  ▼
   ┌──────────────────┐              ┌──────────────────┐
   │ Booking Confirmed│              │ Booking Complete │
   └──────────────────┘              └──────────────────┘
```

## Toggle State Machine

```
                    ┌─────────────────┐
                    │  INITIAL STATE  │
                    │  (Page Load)    │
                    └────────┬────────┘
                             │
                             ▼
              ┌──────────────────────────┐
              │  Read from Database      │
              │  payment_method = ?      │
              └──────────┬───────────────┘
                         │
        ┌────────────────┼────────────────┐
        │                                  │
        ▼                                  ▼
┌───────────────┐                  ┌───────────────┐
│ 'manual'      │                  │ 'automatic'   │
└───────┬───────┘                  └───────┬───────┘
        │                                  │
        ▼                                  ▼
┌───────────────┐                  ┌───────────────┐
│ Toggle OFF    │                  │ Toggle ON     │
│ Orange        │                  │ Green         │
│ QR Icon       │                  │ Card Icon     │
└───────┬───────┘                  └───────┬───────┘
        │                                  │
        │    ┌──────────────┐             │
        │    │ USER CLICKS  │             │
        └───►│    TOGGLE    │◄────────────┘
             └───────┬──────┘
                     │
        ┌────────────┼────────────┐
        │                         │
        ▼                         ▼
┌───────────────┐         ┌───────────────┐
│ Was Manual    │         │ Was Automatic │
│ → Automatic   │         │ → Manual      │
└───────┬───────┘         └───────┬───────┘
        │                         │
        ▼                         ▼
┌───────────────┐         ┌───────────────┐
│ Form Submit   │         │ Form Submit   │
│ value='auto'  │         │ value=null    │
└───────┬───────┘         └───────┬───────┘
        │                         │
        └────────────┬────────────┘
                     │
                     ▼
        ┌────────────────────────┐
        │  POST to Controller    │
        │  togglePaymentMethod() │
        └────────────┬───────────┘
                     │
                     ▼
        ┌────────────────────────┐
        │  Update Database       │
        │  payment_method = new  │
        └────────────┬───────────┘
                     │
                     ▼
        ┌────────────────────────┐
        │  Redirect with Success │
        │  Show Success Message  │
        └────────────┬───────────┘
                     │
                     ▼
        ┌────────────────────────┐
        │  Page Reloads          │
        │  Toggle shows new state│
        └────────────────────────┘
```

## Decision Tree for Hikers

```
                    START: Hiker books trail
                              │
                              ▼
                    ┌─────────────────┐
                    │ Load Booking    │
                    │ Page            │
                    └────────┬────────┘
                             │
                             ▼
                    ┌─────────────────┐
                    │ Select Trail    │
                    └────────┬────────┘
                             │
                             ▼
              ┌──────────────────────────────┐
              │ Fetch Payment Method from API│
              └──────────────┬───────────────┘
                             │
              ┌──────────────┼──────────────┐
              │              │               │
              ▼              ▼               ▼
      ┌──────────┐   ┌──────────┐   ┌──────────┐
      │ Manual   │   │Automatic │   │  Error   │
      │ + QR ✓   │   │ + GW ✓   │   │  Found   │
      └────┬─────┘   └────┬─────┘   └────┬─────┘
           │              │               │
           ▼              ▼               ▼
      SHOW QR        SHOW GATEWAY    DEFAULT TO
      REQUIRE        READY MSG       AUTOMATIC
      UPLOADS        NO EXTRA        SHOW ERROR
           │              │               │
           ▼              ▼               ▼
      Scan & Pay     Click Book      Contact Org
      Upload Proof   → Gateway       for Help
      Enter Ref#     Redirect
           │              │               │
           ▼              ▼               ▼
      SUBMIT         SUBMIT          TRY AGAIN
      Pending        Processing      or CANCEL
      Verification   Auto-Verify
           │              │               │
           └──────────────┴───────────────┘
                          │
                          ▼
                  BOOKING CREATED
```

## Configuration Validation Flow

```
                    Toggle Changed
                          │
                          ▼
              ┌───────────────────┐
              │ New Method = ?    │
              └─────────┬─────────┘
                        │
        ┌───────────────┼───────────────┐
        │                               │
        ▼                               ▼
┌───────────────┐             ┌───────────────┐
│ MANUAL        │             │ AUTOMATIC     │
└───────┬───────┘             └───────┬───────┘
        │                             │
        ▼                             ▼
┌───────────────┐             ┌───────────────┐
│ Check: QR     │             │ Check: Gateway│
│ Code Uploaded?│             │ Configured?   │
└───────┬───────┘             └───────┬───────┘
        │                             │
  ┌─────┼─────┐               ┌───────┼───────┐
  │     │     │               │       │       │
  ▼     ▼     ▼               ▼       ▼       ▼
YES    NO   WARN            YES      NO     WARN
  │     │     │               │       │       │
  │     │     │               │       │       │
  │     └─────┼───────┐       │       └───────┼──────┐
  │           │       │       │               │      │
  ▼           ▼       ▼       ▼               ▼      ▼
READY    INCOMPLETE  │   READY         INCOMPLETE   │
         SHOW        │                  SHOW        │
         WARNING     │                  WARNING     │
  │           │      │       │               │      │
  └───────────┴──────┘       └───────────────┴──────┘
              │                          │
              └──────────┬───────────────┘
                         │
                         ▼
              ┌──────────────────┐
              │ Update Database  │
              │ Log Change       │
              └──────────────────┘
```

## Error Handling Flow

```
                    User Action
                         │
                         ▼
              ┌──────────────────┐
              │ Try Toggle/Save  │
              └─────────┬────────┘
                        │
        ┌───────────────┼───────────────┐
        │               │               │
        ▼               ▼               ▼
┌──────────┐    ┌──────────┐    ┌──────────┐
│ Network  │    │ Server   │    │ Validation│
│ Error    │    │ Error    │    │ Error     │
└────┬─────┘    └────┬─────┘    └────┬──────┘
     │               │               │
     ▼               ▼               ▼
Show Error      Show Error      Show Error
"Check          "Try Again"     "Fix Config"
Connection"         │               │
     │               │               │
     ▼               ▼               ▼
Retry Option    Log Error       Highlight
Allow Manual    Rollback        Fields
Refresh         State           │
     │               │               │
     └───────────────┴───────────────┘
                     │
                     ▼
              User Can Retry
              or Cancel
```

## Timeline Diagram

```
TIME →  [Organizations configures] → [Hikers book] → [Payment processed]

ORG:    Toggle Switch            Monitor Bookings    Verify/Auto-Confirm
        Upload QR/Config GW      Check Payments      Manage Bookings
        Save Settings            Review Status       Track Revenue
        ────────────────────────────────────────────────────────────→

SYSTEM: Save to DB               Fetch Config        Process Payment
        Update Credentials       API Response        Update Status
        Validate Settings        Show UI             Send Notifications
        ────────────────────────────────────────────────────────────→

HIKER:  [Waiting]                Select Trail        Pay
                                 See Payment UI      Upload/Redirect
                                 Complete Booking    Get Confirmation
                                 ────────────────────────────────────→
```
