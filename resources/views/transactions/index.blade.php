<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transactions</title>
</head>
<body>
    <main>
        <h1>Transactions</h1>
        <ul>
            @foreach ($transactions as $transaction)
                <li>
                    {{ $transaction->description ?: ucfirst($transaction->type) }}
                    - {{ $transaction->amount }}
                </li>
            @endforeach
        </ul>

        {{ $transactions->links() }}
    </main>
</body>
</html>