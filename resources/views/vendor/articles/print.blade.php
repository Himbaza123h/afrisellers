<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles List - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; font-weight: bold; }
        .header { margin-bottom: 20px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 10px; }
        .badge-published { background: #d4edda; color: #155724; }
        .badge-draft { background: #f8d7da; color: #721c24; }
        @media print {
            button { display: none; }
            @page { margin: 1cm; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Articles List</h1>
        <p>Generated on: {{ now()->format('M d, Y H:i') }}</p>
        <button onclick="window.print()" style="padding: 10px 20px; margin-bottom: 10px;">Print</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Category</th>
                <th>Author</th>
                <th>Status</th>
                <th>Views</th>
                <th>Published</th>
            </tr>
        </thead>
        <tbody>
            @foreach($articles as $index => $article)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $article->title }}</td>
                    <td>{{ $article->category ?? '-' }}</td>
                    <td>{{ $article->author_name ?? $article->user->name }}</td>
                    <td>
                        <span class="badge badge-{{ $article->status }}">
                            {{ ucfirst($article->status) }}
                        </span>
                    </td>
                    <td>{{ number_format($article->views_count) }}</td>
                    <td>{{ $article->published_at ? $article->published_at->format('M d, Y') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
