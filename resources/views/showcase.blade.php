<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Продукт</title>
</head>
<body>

@if($product)
    <h1>{{ $product->name }}</h1>
    <p>Цена: {{ $product->price }}</p>
@else
    <h1>Продукт не найден</h1>
@endif

</body>
</html>

