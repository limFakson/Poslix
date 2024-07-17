<div class="table-responsive" >
    <table id="extras-table" class="table" style="width: 100%">
        <thead>
            <tr>
                <th>Category</th>                
                <th>Is Multi Selection?</th>
                <th class="not-exported">{{ trans('file.action') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{$item->category_name}}</td>
                    <td>{{$item->is_multi ? 'Multi-Selection' : 'Single-Selection'}}</td>
                    <td>
                        <button
                        wire:confirm="Are you sure you want to delete this post?"
                         class="btn btn-danger" wire:click="delete({{$item->id}})"> Delete </button>                             
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
