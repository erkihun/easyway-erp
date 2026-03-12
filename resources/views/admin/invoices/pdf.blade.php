<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Invoice {{ $invoice->invoice_number }}</title></head>
<body>
    <h1>Invoice {{ $invoice->invoice_number }}</h1>
    <p>Date: {{ $invoice->invoice_date }}</p>
    <p>Status: {{ $invoice->status->value ?? $invoice->status }}</p>
    <p>Total: {{ number_format($invoice->total_amount, 2) }}</p>
</body>
</html>




