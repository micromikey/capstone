# 🔗 Booking & Payment System Architecture

## System Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                      HIKETHERE PLATFORM                          │
│                                                                   │
│  ┌───────────────┐           ┌──────────────────┐               │
│  │   User Auth   │           │   Trail System   │               │
│  │   (Jetstream) │           │   (Organizations)│               │
│  └───────┬───────┘           └────────┬─────────┘               │
│          │                             │                          │
│          └──────────┬──────────────────┘                          │
│                     ▼                                             │
│         ┌───────────────────────┐                                │
│         │   BOOKING SYSTEM      │                                │
│         │  ┌─────────────────┐  │                                │
│         │  │ BookingController│  │                                │
│         │  │   - index()      │  │                                │
│         │  │   - create()     │  │                                │
│         │  │   - store() ─────┼──┼─────┐                         │
│         │  │   - show()       │  │     │                         │
│         │  └─────────────────┘  │     │                         │
│         │         │              │     │                         │
│         │         ▼              │     │                         │
│         │  ┌─────────────────┐  │     │                         │
│         │  │  Booking Model  │  │     │                         │
│         │  │  ┌───────────┐  │  │     │                         │
│         │  │  │ id        │  │  │     │                         │
│         │  │  │ user_id   │  │  │     │                         │
│         │  │  │ trail_id  │  │  │     │                         │
│         │  │  │ batch_id  │  │  │     │                         │
│         │  │  │ date      │  │  │     │                         │
│         │  │  │ party_size│  │  │     │                         │
│         │  │  │ status    │◄─┼──┼─────┼─────────┐               │
│         │  │  │ price_cents│  │  │     │         │               │
│         │  │  └───────────┘  │  │     │         │               │
│         │  └─────────────────┘  │     │         │               │
│         └───────────────────────┘     │         │               │
│                                        │         │               │
│                                        ▼         │               │
│         ┌───────────────────────┐  Redirect     │               │
│         │   PAYMENT SYSTEM      │  to Payment   │               │
│         │  ┌─────────────────┐  │     │         │               │
│         │  │PaymentController│  │     │         │               │
│         │  │  - create() ◄───┼──┼─────┘         │               │
│         │  │  - processPayment│  │               │               │
│         │  │  - success()    │  │               │               │
│         │  │  - webhook() ───┼──┼───────────────┘               │
│         │  └─────────────────┘  │                                │
│         │         │              │                                │
│         │         ▼              │                                │
│         │  ┌─────────────────┐  │                                │
│         │  │BookingPayment   │  │                                │
│         │  │     Model       │  │                                │
│         │  │  ┌───────────┐  │  │                                │
│         │  │  │ id        │  │  │                                │
│         │  │  │ booking_id│──┼──┼──────┐ Links to Booking        │
│         │  │  │ user_id   │  │  │      │                         │
│         │  │  │ fullname  │  │  │      │                         │
│         │  │  │ email     │  │  │      │                         │
│         │  │  │ amount    │  │  │      │                         │
│         │  │  │ payment_  │  │  │      │                         │
│         │  │  │   status  │  │  │      │                         │
│         │  │  │ paymongo_ │  │  │      │                         │
│         │  │  │   link_id │  │  │      │                         │
│         │  │  └───────────┘  │  │      │                         │
│         │  └─────────────────┘  │      │                         │
│         └───────────┬────────────┘      │                         │
│                     │                   │                         │
│                     ▼                   │                         │
│         ┌───────────────────────┐       │                         │
│         │   PAYMONGO API        │       │                         │
│         │  ┌─────────────────┐  │       │                         │
│         │  │ Create Link     │  │       │                         │
│         │  │ Checkout Page   │  │       │                         │
│         │  │ Webhook Events  │  │       │                         │
│         │  └─────────────────┘  │       │                         │
│         └───────────────────────┘       │                         │
│                                          │                         │
└──────────────────────────────────────────┼─────────────────────────┘
                                           │
                                  One-to-One
                                  Relationship

```

## Data Flow Diagram

```
USER JOURNEY
────────────

Step 1: Create Booking
┌─────────────┐
│ User visits │
│ booking     │
│ form        │
└──────┬──────┘
       │
       ▼
┌──────────────────┐
│ Fills:           │
│ • Trail          │
│ • Date           │
│ • Party Size     │
│ • Notes          │
└──────┬───────────┘
       │
       ▼
┌──────────────────┐
│ BookingController│
│ store()          │
│                  │
│ Creates Booking: │
│ ┌──────────────┐ │
│ │status: conf. │ │
│ │price_cents   │ │
│ └──────────────┘ │
└──────┬───────────┘
       │
       ▼
┌──────────────────┐
│ Redirect to:     │
│ /payment/create  │
│ ?booking_id=X    │
└──────┬───────────┘
       │
       │
Step 2: Payment Form
       │
       ▼
┌──────────────────┐
│PaymentController │
│ create()         │
│                  │
│ Loads Booking    │
│ Pre-fills form   │
└──────┬───────────┘
       │
       ▼
┌──────────────────┐
│ User sees:       │
│ ✓ Name (filled)  │
│ ✓ Email (filled) │
│ ✓ Trail (readonly│
│ ✓ Date (readonly)│
│ ✓ Amount (locked)│
└──────┬───────────┘
       │
       │
Step 3: Process Payment
       │
       ▼
┌──────────────────┐
│ User clicks:     │
│ "Proceed to      │
│  Payment"        │
└──────┬───────────┘
       │
       ▼
┌──────────────────┐
│PaymentController │
│ processPayment() │
│                  │
│ Creates/Updates: │
│ ┌──────────────┐ │
│ │BookingPayment│ │
│ │booking_id=X  │ │
│ │status:pending│ │
│ └──────────────┘ │
└──────┬───────────┘
       │
       ▼
┌──────────────────┐
│ Calls PayMongo   │
│ API              │
│                  │
│ Gets checkout_url│
└──────┬───────────┘
       │
       │
Step 4: PayMongo Checkout
       │
       ▼
┌──────────────────┐
│ Redirect to:     │
│ PayMongo         │
│ Checkout Page    │
└──────┬───────────┘
       │
       ▼
┌──────────────────┐
│ User enters:     │
│ • Card Number    │
│ • Expiry         │
│ • CVC            │
│ • Name           │
└──────┬───────────┘
       │
       ▼
┌──────────────────┐
│ Payment          │
│ Processed        │
└──────┬───────────┘
       │
       │
Step 5: Webhook Notification
       │
       ▼
┌──────────────────┐
│ PayMongo sends   │
│ webhook to:      │
│ /payment/webhook │
└──────┬───────────┘
       │
       ▼
┌──────────────────┐
│PaymentController │
│ webhook()        │
│                  │
│ Updates:         │
│ ┌──────────────┐ │
│ │Payment       │ │
│ │status: paid  │ │
│ │paid_at: now  │ │
│ └──────────────┘ │
│ ┌──────────────┐ │
│ │Booking       │ │
│ │status: conf. │ │
│ └──────────────┘ │
└──────┬───────────┘
       │
       │
Step 6: Success Page
       │
       ▼
┌──────────────────┐
│ User redirected  │
│ to success page  │
│                  │
│ Shows:           │
│ ✓ Payment ID     │
│ ✓ Booking ID     │
│ ✓ Amount         │
│ ✓ Status: PAID   │
└──────────────────┘
```

## Database Schema Relationships

```sql
┌─────────────────────────────────────────────────────────────────┐
│                         TABLES                                   │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  users                           trails                          │
│  ├─ id (PK)                      ├─ id (PK)                      │
│  ├─ name                         ├─ user_id (FK→users)           │
│  ├─ email                        ├─ trail_name                   │
│  └─ ...                          ├─ mountain_name                │
│       ▲                          ├─ price                        │
│       │                          └─ ...                          │
│       │                               ▲                          │
│       │                               │                          │
│       │                               │                          │
│  bookings                             │                          │
│  ├─ id (PK) ─────────────────────────┐│                          │
│  ├─ user_id (FK→users) ─────────┐    ││                          │
│  ├─ trail_id (FK→trails) ────────┼────┘│                          │
│  ├─ batch_id (FK→batches)        │     │                          │
│  ├─ event_id (FK→events)         │     │                          │
│  ├─ date                          │     │                          │
│  ├─ party_size                    │     │                          │
│  ├─ status ◄──────────────────────┼─────┼──┐                      │
│  ├─ notes                         │     │  │ Updated by          │
│  ├─ price_cents                   │     │  │ webhook             │
│  ├─ created_at                    │     │  │                      │
│  └─ updated_at                    │     │  │                      │
│       ▲                           │     │  │                      │
│       │                           │     │  │                      │
│       │ ONE-TO-ONE               │     │  │                      │
│       │                           │     │  │                      │
│  booking_payments                 │     │  │                      │
│  ├─ id (PK)                       │     │  │                      │
│  ├─ booking_id (FK→bookings) ─────┘     │  │                      │
│  ├─ user_id (FK→users) ─────────────────┘  │                      │
│  ├─ fullname                               │                      │
│  ├─ email                                  │                      │
│  ├─ phone                                  │                      │
│  ├─ mountain                               │                      │
│  ├─ amount                                 │                      │
│  ├─ hike_date                              │                      │
│  ├─ participants                           │                      │
│  ├─ paymongo_link_id                       │                      │
│  ├─ paymongo_payment_id                    │                      │
│  ├─ payment_status ────────────────────────┘                      │
│  │   (pending/paid/failed/refunded)                              │
│  ├─ paid_at                                                       │
│  ├─ created_at                                                    │
│  └─ updated_at                                                    │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘

INDEXES:
  bookings: user_id, trail_id, batch_id, status
  booking_payments: booking_id, user_id, payment_status
  booking_payments: [booking_id, payment_status] (composite)
```

## API Endpoints

```
┌─────────────────────────────────────────────────────────────────┐
│                     ROUTES (web.php)                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  BOOKING ROUTES (Authenticated)                                 │
│  ├─ GET  /hiker/booking                                          │
│  │       → BookingController@index                              │
│  │       Shows list of user's bookings                          │
│  │                                                               │
│  ├─ GET  /hiker/booking/create                                   │
│  │       → BookingController@create                             │
│  │       Shows booking form                                     │
│  │                                                               │
│  ├─ POST /hiker/booking                                          │
│  │       → BookingController@store                              │
│  │       Creates booking → Redirects to payment                 │
│  │                                                               │
│  └─ GET  /hiker/booking/{booking}                                │
│          → BookingController@show                               │
│          Shows booking details                                  │
│                                                                   │
│  PAYMENT ROUTES (Authenticated)                                 │
│  ├─ GET  /payment/create?booking_id=X                            │
│  │       → PaymentController@create                             │
│  │       Shows payment form (pre-filled if booking_id)          │
│  │                                                               │
│  ├─ POST /payment/process                                        │
│  │       → PaymentController@processPayment                     │
│  │       Creates payment → Redirects to PayMongo                │
│  │                                                               │
│  ├─ GET  /payment/success?payment_id=X                           │
│  │       → PaymentController@success                            │
│  │       Shows payment confirmation                             │
│  │                                                               │
│  └─ POST /payment/webhook (Public - called by PayMongo)          │
│          → PaymentController@webhook                            │
│          Updates payment & booking status                       │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
```

## Status Flow Chart

```
┌──────────────────────────────────────────────────────────────────┐
│                      STATUS LIFECYCLE                             │
└──────────────────────────────────────────────────────────────────┘

BOOKING STATUS:
───────────────
    [Created]
       │
       ▼
  ┌──────────┐
  │confirmed │ ← Initial status (provisional)
  │          │   Awaiting payment
  └────┬─────┘
       │
       ├──────► Payment completed
       │        │
       │        ▼
       │   ┌──────────┐
       │   │confirmed │ ← Final status
       │   │(finalized)
       │   └──────────┘
       │
       └──────► Payment expired (24h+)
                │
                ▼
           ┌──────────┐
           │ expired  │
           └──────────┘


PAYMENT STATUS:
───────────────
    [Created]
       │
       ▼
  ┌──────────┐
  │ pending  │ ← Awaiting user payment
  └────┬─────┘
       │
       ├──────► User pays on PayMongo
       │        │
       │        ▼
       │   ┌──────────┐
       │   │   paid   │ ← Payment successful
       │   └──────────┘
       │
       ├──────► Payment fails
       │        │
       │        ▼
       │   ┌──────────┐
       │   │  failed  │ ← Can retry
       │   └──────────┘
       │
       └──────► Admin initiates refund
                │
                ▼
           ┌──────────┐
           │ refunded │
           └──────────┘
```

## Integration Points

```
┌────────────────────────────────────────────────────────────────┐
│            EXTERNAL INTEGRATIONS                                │
├────────────────────────────────────────────────────────────────┤
│                                                                  │
│  1. PayMongo Payment Gateway                                   │
│     ├─ API Endpoint: https://api.paymongo.com/v1/links        │
│     ├─ Authentication: Basic Auth (secret key)                 │
│     ├─ Actions:                                                │
│     │  ├─ Create payment link                                  │
│     │  ├─ Redirect to checkout                                 │
│     │  └─ Receive webhook notifications                        │
│     └─ Events:                                                 │
│        └─ link.payment.paid                                    │
│                                                                  │
│  2. Email Service (Future)                                     │
│     ├─ Booking confirmation                                    │
│     ├─ Payment receipt                                         │
│     └─ Payment reminder                                        │
│                                                                  │
│  3. SMS Notifications (Future)                                 │
│     ├─ Booking confirmation                                    │
│     └─ Payment reminder                                        │
│                                                                  │
└────────────────────────────────────────────────────────────────┘
```

---

**System Architecture Complete!**
**Last Updated**: October 2, 2025
**Version**: 1.0.0
