<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stripe Payment</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet"/>
    <script src="https://js.stripe.com/v3/"></script>

    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        #card-element {
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            margin-bottom: 15px;
            transition: border-color 0.2s ease;
        }
        #card-element:focus {
            border-color: #007bff;
            outline: none;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        #payment-result {
            margin-top: 15px;
            text-align: center;
            font-weight: bold;
            color: #dc3545; /* Red color for error messages */
        }
        .success {
            color: #28a745; /* Green color for success messages */
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Stripe Payment</h1>
    <form id="payment-form">
        <div id="card-element"><!-- A Stripe Element will be inserted here. --></div>
        <button id="submit">Pay</button>
        <div id="payment-result"></div>
    </form>
</div>

<script>
    const stripe = Stripe("{{ env('STRIPE_KEY') }}"); // Initialize Stripe with your public key
    const elements = stripe.elements();

    const cardElement = elements.create('card');
    cardElement.mount('#card-element');

    const form = document.getElementById('payment-form');
    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        // Call your backend to create a PaymentIntent and get the clientSecret
        const response = await fetch('https://434d-41-45-220-75.ngrok-free.app/api/v1/orders/20/pay', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: {},
        });

        const { clientSecret } = await response.json();

        // Confirm the payment using the clientSecret
        const { error, paymentIntent } = await stripe.confirmCardPayment(clientSecret, {
            payment_method: {
                card: cardElement,
                billing_details: {
                    name: 'Rasha Atta',
                },
            },
        });

        const paymentResult = document.getElementById('payment-result');
        if (error) {
            // Show error to your customer (e.g., insufficient funds)
            paymentResult.innerText = error.message;
            paymentResult.classList.remove('success');
        } else {
            // Payment successful
            paymentResult.innerText = 'Payment successful!';
            paymentResult.classList.add('success');
        }
    });
</script>
</body>
</html>
