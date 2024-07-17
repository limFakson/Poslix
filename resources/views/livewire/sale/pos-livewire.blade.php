<div class="container-fluid">
    <div class="row">
        <audio id="mysoundclip1" preload="auto">
            <source src="{{ secure_url('beep/beep-timber.mp3') }}">
            </source>
        </audio>
        <audio id="mysoundclip2" preload="auto">
            <source src="{{ secure_url('beep/beep-07.mp3') }}">
            </source>
        </audio>
        <div class="col-md-6" style="min-height: 100vh;">
            <div class="card" style="min-height: 93vh;">
                <div class="card-body" style="padding-bottom: 0">
                    <div class="row" wire:loading.attr="disabled">
                        <div class="col-md-12">
                            <div class="row" wire:ignore>
                                @if ($lims_pos_setting_data->is_table)
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            @if ($lims_pos_setting_data)
                                                <input type="hidden" name="warehouse_id_hidden"
                                                    value="{{ $lims_pos_setting_data->warehouse_id }}">
                                            @endif
                                            <select required id="warehouse_id" name="warehouse_id"
                                                class="selectpicker form-control" data-live-search="true"
                                                data-live-search-style="begins" title="Select warehouse..."
                                                wire:model.live="warehouse_id">
                                                @foreach ($lims_warehouse_list as $warehouse)
                                                    <option value="{{ $warehouse->id }}"
                                                        {{ $warehouse_id == $warehouse->id ? 'selected' : '' }}>
                                                        {{ $warehouse->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <select required id="table_id" name="table_id"
                                                class="selectpicker form-control" data-live-search="true"
                                                data-live-search-style="begins" title="Select table..."
                                                wire:model="table_id">
                                                @foreach ($lims_table_list as $table)
                                                    <option {{ $table_id == $table->id ? 'selected' : '' }}
                                                        value="{{ $table->id }}">{{ $table->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-md-6 d-none">
                                        <div class="form-group">
                                            @if ($lims_pos_setting_data)
                                                <input type="hidden" name="warehouse_id_hidden"
                                                    value="{{ $lims_pos_setting_data->warehouse_id }}">
                                            @endif
                                            <select required id="warehouse_id" name="warehouse_id"
                                                wire:model="warehouse_id" class="selectpicker form-control"
                                                data-live-search="true" data-live-search-style="begins"
                                                title="Select warehouse...">
                                                @foreach ($lims_warehouse_list as $warehouse)
                                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-4 d-none">
                                    <div class="form-group">
                                        @if ($lims_pos_setting_data)
                                            <input type="hidden" name="biller_id_hidden"
                                                value="{{ $lims_pos_setting_data->biller_id }}">
                                        @endif
                                        <select required id="biller_id" name="biller_id"
                                            class="selectpicker form-control" data-live-search="true"
                                            data-live-search-style="begins" title="Select Biller..."
                                            wire:model="biller_id">
                                            @foreach ($lims_biller_list as $biller)
                                                <option value="{{ $biller->id }}">
                                                    {{ $biller->name . ' (' . $biller->company_name . ')' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        @if ($lims_pos_setting_data)
                                            <input type="hidden" name="customer_id_hidden"
                                                value="{{ $lims_pos_setting_data->customer_id }}">
                                        @endif
                                        <div class="input-group pos">
                                            @if ($customer_active)
                                                <select required name="customer_id" id="customer_id"
                                                    wire:model.live="customer_id" class="selectpicker form-control"
                                                    data-live-search="true" title="Select customer..."
                                                    style="width: 100px">
                                                    <?php
                                                    $deposit = [];
                                                    $points = [];
                                                    ?>
                                                    @foreach ($lims_customer_list as $cust)
                                                        @php
                                                            $deposit[$cust->id] = $cust->deposit - $cust->expense;

                                                            $points[$cust->id] = $cust->points;
                                                        @endphp
                                                        <option {{ $customer_id == $cust->id ? 'selected' : '' }}
                                                            value="{{ $cust->id }}">
                                                            {{ $cust->name . ' (' . $cust->phone_number . ')' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="button" class="btn btn-default btn-sm"
                                                    data-toggle="modal" data-target="#addCustomer"><i
                                                        class="dripicons-plus"></i></button>
                                            @else
                                                <?php
                                                $deposit = [];
                                                $points = [];
                                                ?>
                                                <select required name="customer_id" id="customer_id"
                                                    wire:model="customer_id" class="selectpicker form-control"
                                                    data-live-search="true" title="Select customer...">
                                                    @foreach ($lims_customer_list as $customer)
                                                        @php
                                                            $deposit[$customer->id] =
                                                                $customer->deposit - $customer->expense;

                                                            $points[$customer->id] = $customer->points;
                                                        @endphp
                                                        <option value="{{ $customer->id }}">
                                                            {{ $customer->name . ' (' . $customer->phone_number . ')' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @foreach ($custom_fields as $field)
                                    @if (!$field->is_admin || \Auth::user()->role_id == 1)
                                        <div class="{{ 'col-md-' . $field->grid_value }}">
                                            <div class="form-group">
                                                <label>{{ $field->name }}</label>
                                                @if ($field->type == 'text')
                                                    <input type="text"
                                                        name="{{ str_replace(' ', '_', strtolower($field->name)) }}"
                                                        value="{{ $field->default_value }}" class="form-control"
                                                        @if ($field->is_required) {{ 'required' }} @endif>
                                                @elseif($field->type == 'number')
                                                    <input type="number"
                                                        name="{{ str_replace(' ', '_', strtolower($field->name)) }}"
                                                        value="{{ $field->default_value }}" class="form-control"
                                                        @if ($field->is_required) {{ 'required' }} @endif>
                                                @elseif($field->type == 'textarea')
                                                    <textarea rows="5" name="{{ str_replace(' ', '_', strtolower($field->name)) }}"
                                                        value="{{ $field->default_value }}" class="form-control"
                                                        @if ($field->is_required) {{ 'required' }} @endif></textarea>
                                                @elseif($field->type == 'checkbox')
                                                    <br>
                                                    <?php $option_values = explode(',', $field->option_value); ?>
                                                    @foreach ($option_values as $value)
                                                        <label>
                                                            <input type="checkbox"
                                                                name="{{ str_replace(' ', '_', strtolower($field->name)) }}[]"
                                                                value="{{ $value }}"
                                                                @if ($value == $field->default_value) {{ 'checked' }} @endif
                                                                @if ($field->is_required) {{ 'required' }} @endif>
                                                            {{ $value }}
                                                        </label>
                                                        &nbsp;
                                                    @endforeach
                                                @elseif($field->type == 'radio_button')
                                                    <br>
                                                    <?php $option_values = explode(',', $field->option_value); ?>
                                                    @foreach ($option_values as $value)
                                                        <label class="radio-inline">
                                                            <input type="radio"
                                                                name="{{ str_replace(' ', '_', strtolower($field->name)) }}"
                                                                value="{{ $value }}"
                                                                @if ($value == $field->default_value) {{ 'checked' }} @endif
                                                                @if ($field->is_required) {{ 'required' }} @endif>
                                                            {{ $value }}
                                                        </label>
                                                        &nbsp;
                                                    @endforeach
                                                @elseif($field->type == 'select')
                                                    <?php $option_values = explode(',', $field->option_value); ?>
                                                    <select class="form-control"
                                                        name="{{ str_replace(' ', '_', strtolower($field->name)) }}"
                                                        @if ($field->is_required) {{ 'required' }} @endif>
                                                        @foreach ($option_values as $value)
                                                            <option value="{{ $value }}"
                                                                @if ($value == $field->default_value) {{ 'selected' }} @endif>
                                                                {{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                @elseif($field->type == 'multi_select')
                                                    <?php $option_values = explode(',', $field->option_value); ?>
                                                    <select class="form-control"
                                                        name="{{ str_replace(' ', '_', strtolower($field->name)) }}[]"
                                                        @if ($field->is_required) {{ 'required' }} @endif
                                                        multiple>
                                                        @foreach ($option_values as $value)
                                                            <option value="{{ $value }}"
                                                                @if ($value == $field->default_value) {{ 'selected' }} @endif>
                                                                {{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                @elseif($field->type == 'date_picker')
                                                    <input type="text"
                                                        name="{{ str_replace(' ', '_', strtolower($field->name)) }}"
                                                        value="{{ $field->default_value }}" class="form-control date"
                                                        @if ($field->is_required) {{ 'required' }} @endif>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                <div class="col-md-12">
                                    @if ($warehouse_id)
                                        <livewire:sale.autocomplete-search :warehouse_id="$warehouse_id" />
                                    @endif
                                    {{-- <div class="search-box form-group">
                                        <input type="text" name="product_code_name" id="lims_productcodeSearch"
                                            placeholder="Scan/Search product by name/code" class="form-control" />
                                    </div> --}}
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="table-responsive transaction-list">
                                    <table id="myTable"
                                        class="table table-hover table-striped order-list table-fixed">
                                        <thead>
                                            <tr>
                                                <th class="col-sm-2">{{ trans('file.product') }}</th>
                                                <th class="col-sm-2">{{ trans('file.Batch No') }}</th>
                                                <th class="col-sm-2">{{ trans('file.Price') }}</th>
                                                <th class="col-sm-3">{{ trans('file.Quantity') }}</th>
                                                <th class="col-sm-3">{{ trans('file.Subtotal') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody-id">
                                            @if ($items)
                                                @foreach ($items as $item)
                                                    @if (isset($item['name']))
                                                        <tr wire:key="tr-{{ time() }}">
                                                            <td class="col-sm-2 product-title">
                                                                <button type="button"
                                                                    class="edit-product btn btn-link"
                                                                    id="btnEdit{{ $loop->index }}"
                                                                    data-toggle="modal" {{-- wire:click="$dispatch('open-qty-modal',{index:{{ $loop->index }}})" --}}
                                                                    data-target="#editModal{{ $loop->index }}">
                                                                    <span style="margin-left: -19px;">
                                                                        <strong>{{ $item['name'] ?? '' }}</strong>
                                                                    </span>
                                                                </button>
                                                                @if (isset($item['code']))
                                                                    <br>{{ $item['code'] ?? '' }}
                                                                @endif
                                                                <p>In Stock: <span
                                                                        class="in-stock">{{ $item['in_stock'] }}</span>
                                                                </p>
                                                            </td>
                                                            <td class="col-sm-2"><input type="text"
                                                                    class="form-control batch-no" disabled="">
                                                                <input type="hidden" class="product-batch-id"
                                                                    name="product_batch_id[]"
                                                                    value="{{ $item['batch'] }}">
                                                            </td>
                                                            <td class="col-sm-2 product-price">{{ $item['price'] }}
                                                            </td>
                                                            <td class="col-sm-3">
                                                                <div class="input-group">
                                                                    <span class="input-group-btn">
                                                                        <button type="button"
                                                                            wire:loading.attr="disabled"
                                                                            class="btn btn-default minus">
                                                                            <span class="dripicons-minus"
                                                                                wire:click="minus({{ $loop->index }})"></span>
                                                                        </button>
                                                                    </span>
                                                                    <input type="text" name="qty"
                                                                        class="form-control qty numkey input-number"
                                                                        step="any" value="{{ $item['qty'] }}"
                                                                        required="" />
                                                                    <span class="input-group-btn">
                                                                        <button type="button"
                                                                            wire:loading.attr="disabled"
                                                                            class="btn btn-default plus"><span
                                                                                class="dripicons-plus"
                                                                                wire:click="plus({{ $loop->index }})"></span>
                                                                        </button>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td class="col-sm-2 sub-total">{{ $item['total'] }}</td>
                                                            <td class="col-sm-1">
                                                                <button type="button"
                                                                    class="ibtnDel btn btn-danger btn-sm"
                                                                    wire:click="delete({{ $loop->index }})"
                                                                    wire:confirm="Are you sure to delete?">
                                                                    <i class="dripicons-cross"></i>
                                                                </button>

                                                                <div id="editModal{{ $loop->index }}" tabindex="-1"
                                                                    role="dialog" aria-labelledby="exampleModalLabel"
                                                                    aria-hidden="true" class="modal fade text-left"
                                                                    wire:key="modal-{{ time() }}">
                                                                    <div role="document" class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 id="modal_header"
                                                                                    class="modal-title">
                                                                                </h5>
                                                                                <button type="button"
                                                                                    data-dismiss="modal"
                                                                                    aria-label="Close"
                                                                                    class="close"><span
                                                                                        aria-hidden="true"><i
                                                                                            class="dripicons-cross"></i></span></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <div class="row modal-element">
                                                                                    <div class="col-md-4 form-group">
                                                                                        <label>{{ trans('file.Quantity') }}</label>
                                                                                        <input type="text"
                                                                                            name="edit_qty"
                                                                                            class="form-control numkey"
                                                                                            wire:model="items.{{ $loop->index }}.qty">
                                                                                    </div>
                                                                                    <div class="col-md-4 form-group">
                                                                                        <label>{{ trans('file.Unit Discount') }}</label>
                                                                                        <input type="text"
                                                                                            name="edit_discount"
                                                                                            class="form-control numkey"
                                                                                            wire:model="items.{{ $loop->index }}.discount">
                                                                                    </div>
                                                                                    <div class="col-md-4 form-group">
                                                                                        <label>{{ trans('file.Unit Price') }}</label>
                                                                                        <input type="text"
                                                                                            name="edit_unit_price"
                                                                                            class="form-control numkey"
                                                                                            wire:model="items.{{ $loop->index }}.display_unit_price"
                                                                                            step="any">
                                                                                    </div>
                                                                                    <?php
                                                                                    $tax_name_all[] = 'No Tax';
                                                                                    $tax_rate_all[] = 0;
                                                                                    foreach ($lims_tax_list as $tax) {
                                                                                        $tax_name_all[] = $tax->name;
                                                                                        $tax_rate_all[] = $tax->rate;
                                                                                    }
                                                                                    ?>
                                                                                    <div class="col-md-4 form-group">
                                                                                        <label>{{ trans('file.Tax Rate') }}</label>
                                                                                        <select name="edit_tax_rate"
                                                                                            class="form-control"
                                                                                            wire:model="items.{{ $loop->index }}.tax_rate">
                                                                                            <option value="0">No
                                                                                                Tax</option>
                                                                                            @foreach ($lims_tax_list as $key => $tax)
                                                                                                <option
                                                                                                    value="{{ $tax->rate }}|{{ $tax->id }}"
                                                                                                    data-rate="{{ $tax->rate }}">
                                                                                                    {{ $tax->name }}
                                                                                                </option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                    </div>

                                                                                    @if (isset($item['units']) && count($item['units']) > 0)
                                                                                        <div id="edit_unit"
                                                                                            class="col-md-4 form-group">
                                                                                            <label>{{ trans('file.Product Unit') }}</label>
                                                                                            <select name="edit_unit"
                                                                                                wire:model="items.{{ $loop->index }}.unit_id"
                                                                                                class="form-control">

                                                                                                @foreach ($item['units'] as $unit)
                                                                                                    <option
                                                                                                        value="{{ $unit->id }}">
                                                                                                        {{ $unit->unit_name }}
                                                                                                    </option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                                @if (isset($item['extra_categories']))
                                                                                    @include(
                                                                                        'backend.sale.extra-categories',
                                                                                        [
                                                                                            'extraCategories' =>
                                                                                                $item[
                                                                                                    'extra_categories'
                                                                                                ],
                                                                                            'index' =>
                                                                                                $loop->index,
                                                                                        ]
                                                                                    )
                                                                                @endif
                                                                                <button type="button"
                                                                                    wire:click="updateCart({{ $loop->index }})"
                                                                                    name="update_btn"
                                                                                    class="btn btn-primary">{{ trans('file.update') }}</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @if (!empty($item['extra_names']))
                                                            <tr>
                                                                <td colspan="6">
                                                                    @foreach ($item['extra_names'] as $extraName)
                                                                        {{$extraName}}
                                                                        @if(!$loop->last)
                                                                        <br/>
                                                                        @endif
                                                                    @endforeach
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 totals"
                        style="position: absolute; bottom: 75px;left: 0;border-top: 2px solid #e4e6fc; padding-top: 10px;">
                        @php
                            $items = collect($items);
                        @endphp
                        <div class="row">
                            <div class="col-sm-4">
                                <span class="totals-title">{{ trans('file.Items') }}</span><span
                                    id="item">{{ $items->count() }} ({{ $items->sum('qty') }})</span>
                            </div>
                            <div class="col-sm-4">
                                <span class="totals-title">{{ trans('file.Total') }}</span><span
                                    id="subtotal">{{ number_format($items->sum('total'), $general_setting->decimal, '.', '') }}</span>
                            </div>
                            <div class="col-sm-4">
                                <span class="totals-title">{{ trans('file.Discount') }} <button type="button"
                                        class="btn btn-link btn-sm" data-toggle="modal"
                                        data-target="#order-discount-modal"> <i
                                            class="dripicons-document-edit"></i></button></span><span
                                    id="discount">{{ number_format($total_discount, $general_setting->decimal, '.', '') }}</span>
                            </div>
                            <div class="col-sm-4">
                                <span class="totals-title">{{ trans('file.Coupon') }} <button type="button"
                                        class="btn btn-link btn-sm" data-toggle="modal"
                                        data-target="#coupon-modal"><i
                                            class="dripicons-document-edit"></i></button></span><span
                                    id="coupon-text">{{ number_format($coupon_discount, $general_setting->decimal, '.', '') }}</span>
                            </div>
                            <div class="col-sm-4">
                                <span class="totals-title">{{ trans('file.Tax') }}
                                    <button type="button" class="btn btn-link btn-sm" data-toggle="modal"
                                        data-target="#order-tax">
                                        <i class="dripicons-document-edit"></i>
                                    </button>
                                </span>
                                <span
                                    id="tax">{{ number_format($order_tax, $general_setting->decimal, '.', '') }}</span>
                            </div>
                            <div class="col-sm-4">
                                <span class="totals-title">{{ trans('file.Shipping') }}
                                    <button type="button" class="btn btn-link btn-sm" data-toggle="modal"
                                        data-target="#shipping-cost-modal">
                                        <i class="dripicons-document-edit"></i>
                                    </button>
                                </span>
                                <span
                                    id="shipping-cost">{{ number_format($shipping_cost, $general_setting->decimal, '.', '') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="payment-amount">
                    <h2>{{ trans('file.grand total') }} <span
                            id="grand-total">{{ number_format($grand_total, $general_setting->decimal, '.', '') }}</span>
                    </h2>
                </div>
                <div class="payment-options">
                    @if (in_array('card', $options))
                        <div class="column-5">
                            <button style="background: #0984e3" type="button"
                                class="btn btn-sm btn-custom payment-btn" data-toggle="modal" id="setDfltPay"
                                data-target="#add-payment">
                                <i class="fa fa-credit-card"></i>
                                {{ trans('file.Pay') }}
                            </button>
                        </div>
                    @endif
                    @if (in_array('paypal', $options) &&
                            $lims_pos_setting_data &&
                            strlen($lims_pos_setting_data->paypal_live_api_username) > 0 &&
                            strlen($lims_pos_setting_data->paypal_live_api_password) > 0 &&
                            strlen($lims_pos_setting_data->paypal_live_api_secret) > 0)
                        <div class="column-5">
                            <button style="background-color: #213170" type="button"
                                class="btn btn-sm btn-custom payment-btn" data-toggle="modal"
                                wire:click="$set('sale_status',1)" data-target="#add-payment" id="paypal-btn"><i
                                    class="fa fa-paypal"></i>
                                {{ trans('file.PayPal') }}</button>
                        </div>
                    @endif
                    <div class="column-5">
                        <button style="background-color: #e28d02" type="button" class="btn btn-sm btn-custom"
                            id="draft-btn"><i class="dripicons-flag"></i> {{ trans('file.Draft') }}</button>
                    </div>
                    @if ($lims_reward_point_setting_data && $lims_reward_point_setting_data->is_active)
                        <div class="column-5">
                            <button style="background-color: #319398" type="button"
                                class="btn btn-sm btn-custom payment-btn" data-toggle="modal"
                                data-target="#add-payment" id="point-btn"><i class="dripicons-rocket"></i>
                                {{ trans('file.Points') }}</button>
                        </div>
                    @endif
                    <div class="column-5">
                        <button style="background-color: #d63031;" type="button" class="btn btn-sm btn-custom"
                            id="cancel-btn" onclick="return confirmCancel()"><i class="fa fa-close"></i>
                            {{ trans('file.Cancel') }}</button>
                    </div>
                    <div class="column-5">
                        <button style="background-color: #ffc107;" type="button" class="btn btn-sm btn-custom"
                            data-toggle="modal" data-target="#recentTransaction"><i class="dripicons-clock"></i>
                            {{ trans('file.Recent Transaction') }}</button>
                    </div>

                    <header class="p-0 float-right">
                        <nav class="navbar">

                            <div class="navbar-header">
                                <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
                                    <?php
                                    $general_setting_permission = $permission_list->where('name', 'general_setting')->first();
                                    $general_setting_permission_active = DB::table('role_has_permissions')
                                        ->where([['permission_id', $general_setting_permission->id], ['role_id', Auth::user()->role_id]])
                                        ->first();

                                    $pos_setting_permission = $permission_list->where('name', 'pos_setting')->first();

                                    $pos_setting_permission_active = DB::table('role_has_permissions')
                                        ->where([['permission_id', $pos_setting_permission->id], ['role_id', Auth::user()->role_id]])
                                        ->first();
                                    ?>
                                    {{-- <li class="nav-item ml-4"><a id="btnFullscreen" data-toggle="tooltip" title="Full Screen"><i class="dripicons-expand"></i></a></li>

                            @if ($pos_setting_permission_active)
                            <li class="nav-item"><a class="dropdown-item" data-toggle="tooltip" href="{{route('setting.pos')}}" title="{{trans('file.POS Setting')}}"><i class="dripicons-gear"></i></a> </li>
                            @endif
                            <li class="nav-item">
                                <a href="{{route('sales.printLastReciept')}}" data-toggle="tooltip" title="{{trans('file.Print Last Reciept')}}"><i class="dripicons-print"></i></a>
                            </li> --}}
                                    <li class="nav-item">
                                        <a href="" id="register-details-btn" data-toggle="tooltip"
                                            title="{{ trans('file.Cash Register Details') }}"><i
                                                class="dripicons-briefcase"></i></a>
                                    </li>
                                    <?php
                                    $today_sale_permission = $permission_list->where('name', 'today_sale')->first();
                                    $today_sale_permission_active = DB::table('role_has_permissions')
                                        ->where([['permission_id', $today_sale_permission->id], ['role_id', Auth::user()->role_id]])
                                        ->first();

                                    $today_profit_permission = $permission_list->where('name', 'today_profit')->first();
                                    $today_profit_permission_active = DB::table('role_has_permissions')
                                        ->where([['permission_id', $today_profit_permission->id], ['role_id', Auth::user()->role_id]])
                                        ->first();
                                    ?>

                                    {{-- @if ($today_sale_permission_active)
                            <li class="nav-item">
                                <a href="" id="today-sale-btn" data-toggle="tooltip" title="{{trans('file.Today Sale')}}"><i class="dripicons-shopping-bag"></i></a>
                            </li>
                            @endif
                            @if ($today_profit_permission_active)
                            <li class="nav-item">
                                <a href="" id="today-profit-btn" data-toggle="tooltip" title="{{trans('file.Today Profit')}}"><i class="dripicons-graph-line"></i></a>
                            </li>
                            @endif --}}
                                    @if ($alert_product_count + count(\Auth::user()->unreadNotifications) > 0)
                                        <li class="nav-item" id="notification-icon">
                                            <a rel="nofollow" data-toggle="tooltip"
                                                title="{{ __('Notifications') }}" class="nav-link dropdown-item"><i
                                                    class="dripicons-bell"></i><span
                                                    class="badge badge-danger notification-number">{{ $alert_product_count + count(\Auth::user()->unreadNotifications) }}</span>
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </a>
                                            <ul class="right-sidebar" user="menu">
                                                <li class="notifications">
                                                    <a href="{{ route('report.qtyAlert') }}"
                                                        class="btn btn-link">{{ $alert_product_count }} product
                                                        exceeds
                                                        alert quantity</a>
                                                </li>
                                                @foreach (\Auth::user()->unreadNotifications as $key => $notification)
                                                    <li class="notifications">
                                                        <a href="#"
                                                            class="btn btn-link">{{ $notification->data['message'] }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endif
                                    <li class="nav-item">
                                        <a rel="nofollow" data-toggle="tooltip" class="nav-link dropdown-item"><i
                                                class="dripicons-user"></i>
                                            <span>{{ ucfirst(Auth::user()->name) }}</span> <i
                                                class="fa fa-angle-down"></i>
                                        </a>
                                        <ul class="right-sidebar">
                                            <li>
                                                <a href="{{ route('user.profile', ['id' => Auth::id()]) }}"><i
                                                        class="dripicons-user"></i>
                                                    {{ trans('file.profile') }}</a>
                                            </li>
                                            @if ($general_setting_permission_active)
                                                <li>
                                                    <a href="{{ route('setting.general') }}"><i
                                                            class="dripicons-gear"></i>
                                                        {{ trans('file.settings') }}</a>
                                                </li>
                                            @endif
                                            <li>
                                                <a
                                                    href="{{ secure_url('my-transactions/' . date('Y') . '/' . date('m')) }}"><i
                                                        class="dripicons-swap"></i>
                                                    {{ trans('file.My Transaction') }}</a>
                                            </li>
                                            @if (Auth::user()->role_id != 5)
                                                <li>
                                                    <a
                                                        href="{{ secure_url('holidays/my-holiday/' . date('Y') . '/' . date('m')) }}"><i
                                                            class="dripicons-vibrate"></i>
                                                        {{ trans('file.My Holiday') }}</a>
                                                </li>
                                            @endif
                                            <li>
                                                <a href="{{ route('logout') }}"
                                                    onclick="event.preventDefault();
                                                            document.getElementById('logout-form').submit();"><i
                                                        class="dripicons-power"></i>
                                                    {{ trans('file.logout') }}
                                                </a>

                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                    </header>
                </div>
            </div>
        </div>

        <!-- product list -->
        <div class="col-md-6">
            <!-- navbar-->
            <header class="d-none">
                <nav class="navbar">

                    <div class="navbar-header">
                        <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
                            <div class="dropdown">
                                <a class="btn-pos btn-sm" type="button" data-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="dripicons-plus"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <?php
                                    $category_permission_active = $role_has_permissions_list->where('name', 'category')->first();
                                    ?>
                                    @if ($category_permission_active)
                                        <li class="dropdown-item"><a data-toggle="modal"
                                                data-target="#category-modal">{{ __('file.Add Category') }}</a>
                                        </li>
                                    @endif
                                    <?php
                                    $add_permission_active = $role_has_permissions_list->where('name', 'products-add')->first();
                                    ?>
                                    @if ($add_permission_active)
                                        <li class="dropdown-item"><a
                                                href="{{ route('products.create') }}">{{ __('file.add_product') }}</a>
                                        </li>
                                    @endif
                                    <?php
                                    $add_permission_active = $role_has_permissions_list->where('name', 'purchases-add')->first();
                                    ?>
                                    @if ($add_permission_active)
                                        <li class="dropdown-item"><a
                                                href="{{ route('purchases.create') }}">{{ trans('file.Add Purchase') }}</a>
                                        </li>
                                    @endif
                                    <?php
                                    $sale_add_permission_active = $role_has_permissions_list->where('name', 'sales-add')->first();
                                    ?>
                                    @if ($sale_add_permission_active)
                                        <li class="dropdown-item"><a
                                                href="{{ route('sales.create') }}">{{ trans('file.Add Sale') }}</a>
                                        </li>
                                    @endif
                                    <?php
                                    $expense_add_permission_active = $role_has_permissions_list->where('name', 'expenses-add')->first();
                                    ?>
                                    @if ($expense_add_permission_active)
                                        <li class="dropdown-item"><a data-toggle="modal"
                                                data-target="#expense-modal"> {{ trans('file.Add Expense') }}</a>
                                        </li>
                                    @endif
                                    <?php
                                    $quotation_add_permission_active = $role_has_permissions_list->where('name', 'quotes-add')->first();
                                    ?>
                                    @if ($quotation_add_permission_active)
                                        <li class="dropdown-item"><a
                                                href="{{ route('quotations.create') }}">{{ trans('file.Add Quotation') }}</a>
                                        </li>
                                    @endif
                                    <?php
                                    $transfer_add_permission_active = $role_has_permissions_list->where('name', 'transfers-add')->first();
                                    ?>
                                    @if ($transfer_add_permission_active)
                                        <li class="dropdown-item"><a
                                                href="{{ route('transfers.create') }}">{{ trans('file.Add Transfer') }}</a>
                                        </li>
                                    @endif
                                    <?php
                                    $return_add_permission_active = $role_has_permissions_list->where('name', 'returns-add')->first();
                                    ?>
                                    @if ($return_add_permission_active)
                                        <li class="dropdown-item"><a href="#" data-toggle="modal"
                                                data-target="#add-sale-return">
                                                {{ trans('file.Add Return') }}</a></li>
                                    @endif
                                    <?php
                                    $purchase_return_add_permission_active = $role_has_permissions_list->where('name', 'purchase-return-add')->first();
                                    ?>
                                    @if ($purchase_return_add_permission_active)
                                        <li class="dropdown-item"><a href="#" data-toggle="modal"
                                                data-target="#add-purchase-return">
                                                {{ trans('file.Add Purchase Return') }}</a></li>
                                    @endif
                                    <?php
                                    $user_add_permission_active = $role_has_permissions_list->where('name', 'users-add')->first();
                                    ?>
                                    @if ($user_add_permission_active)
                                        <li class="dropdown-item"><a
                                                href="{{ route('user.create') }}">{{ trans('file.Add User') }}</a>
                                        </li>
                                    @endif
                                    <?php
                                    $customer_add_permission_active = $role_has_permissions_list->where('name', 'customers-add')->first();
                                    ?>
                                    @if ($customer_add_permission_active)
                                        <li class="dropdown-item"><a
                                                href="{{ route('customer.create') }}">{{ trans('file.Add Customer') }}</a>
                                        </li>
                                    @endif
                                    <?php
                                    $biller_add_permission_active = $role_has_permissions_list->where('name', 'billers-add')->first();
                                    ?>
                                    @if ($biller_add_permission_active)
                                        <li class="dropdown-item"><a
                                                href="{{ route('biller.create') }}">{{ trans('file.Add Biller') }}</a>
                                        </li>
                                    @endif
                                    <?php
                                    $supplier_add_permission_active = $role_has_permissions_list->where('name', 'suppliers-add')->first();
                                    ?>
                                    @if ($supplier_add_permission_active)
                                        <li class="dropdown-item"><a
                                                href="{{ route('supplier.create') }}">{{ trans('file.Add Supplier') }}</a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            <li class="nav-item ml-4"><a id="btnFullscreen" data-toggle="tooltip"
                                    title="Full Screen"><i class="dripicons-expand"></i></a></li>
                            <?php
                            $general_setting_permission = $permission_list->where('name', 'general_setting')->first();
                            $general_setting_permission_active = DB::table('role_has_permissions')
                                ->where([['permission_id', $general_setting_permission->id], ['role_id', Auth::user()->role_id]])
                                ->first();

                            $pos_setting_permission = $permission_list->where('name', 'pos_setting')->first();

                            $pos_setting_permission_active = DB::table('role_has_permissions')
                                ->where([['permission_id', $pos_setting_permission->id], ['role_id', Auth::user()->role_id]])
                                ->first();
                            ?>
                            @if ($pos_setting_permission_active)
                                <li class="nav-item"><a class="dropdown-item" data-toggle="tooltip"
                                        href="{{ route('setting.pos') }}"
                                        title="{{ trans('file.POS Setting') }}"><i class="dripicons-gear"></i></a>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a href="{{ route('sales.printLastReciept') }}" data-toggle="tooltip"
                                    title="{{ trans('file.Print Last Reciept') }}"><i
                                        class="dripicons-print"></i></a>
                            </li>
                            <li class="nav-item">
                                <a href="" id="register-details-btn" data-toggle="tooltip"
                                    title="{{ trans('file.Cash Register Details') }}"><i
                                        class="dripicons-briefcase"></i></a>
                            </li>
                            <?php
                            $today_sale_permission = $permission_list->where('name', 'today_sale')->first();
                            $today_sale_permission_active = DB::table('role_has_permissions')
                                ->where([['permission_id', $today_sale_permission->id], ['role_id', Auth::user()->role_id]])
                                ->first();

                            $today_profit_permission = $permission_list->where('name', 'today_profit')->first();
                            $today_profit_permission_active = DB::table('role_has_permissions')
                                ->where([['permission_id', $today_profit_permission->id], ['role_id', Auth::user()->role_id]])
                                ->first();
                            ?>

                            @if ($today_sale_permission_active)
                                <li class="nav-item">
                                    <a href="" id="today-sale-btn" data-toggle="tooltip"
                                        title="{{ trans('file.Today Sale') }}"><i
                                            class="dripicons-shopping-bag"></i></a>
                                </li>
                            @endif
                            @if ($today_profit_permission_active)
                                <li class="nav-item">
                                    <a href="" id="today-profit-btn" data-toggle="tooltip"
                                        title="{{ trans('file.Today Profit') }}"><i
                                            class="dripicons-graph-line"></i></a>
                                </li>
                            @endif

                            <li class="nav-item" id="notification-icon">
                                <a rel="nofollow" data-toggle="tooltip" title="{{ __('Notifications') }}"
                                    class="nav-link dropdown-item"><i class="dripicons-bell"></i><span
                                        class="badge badge-danger notification-number">{{ $alert_product_count + count(\Auth::user()->unreadNotifications) }}</span>
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </a>
                                <ul class="right-sidebar" user="menu">
                                    <li class="notifications">
                                        <a href="{{ route('report.qtyAlert') }}"
                                            class="btn btn-link">{{ $alert_product_count }} product exceeds alert
                                            quantity</a>
                                    </li>
                                    @foreach (\Auth::user()->unreadNotifications as $key => $notification)
                                        <li class="notifications">
                                            <a href="#"
                                                class="btn btn-link">{{ $notification->data['message'] }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                            @if ($alert_product_count + count(\Auth::user()->unreadNotifications) > 0)
                            @endif
                            <li class="nav-item">
                                <a rel="nofollow" data-toggle="tooltip" class="nav-link dropdown-item"><i
                                        class="dripicons-user"></i>
                                    <span>{{ ucfirst(Auth::user()->name) }}</span> <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="right-sidebar">
                                    <li>
                                        <a href="{{ route('user.profile', ['id' => Auth::id()]) }}"><i
                                                class="dripicons-user"></i> {{ trans('file.profile') }}</a>
                                    </li>
                                    @if ($general_setting_permission_active)
                                        <li>
                                            <a href="{{ route('setting.general') }}"><i class="dripicons-gear"></i>
                                                {{ trans('file.settings') }}</a>
                                        </li>
                                    @endif
                                    <li>
                                        <a href="{{ secure_url('my-transactions/' . date('Y') . '/' . date('m')) }}"><i
                                                class="dripicons-swap"></i>
                                            {{ trans('file.My Transaction') }}</a>
                                    </li>
                                    @if (Auth::user()->role_id != 5)
                                        <li>
                                            <a
                                                href="{{ secure_url('holidays/my-holiday/' . date('Y') . '/' . date('m')) }}"><i
                                                    class="dripicons-vibrate"></i>
                                                {{ trans('file.My Holiday') }}</a>
                                        </li>
                                    @endif
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();"><i
                                                class="dripicons-power"></i>
                                            {{ trans('file.logout') }}
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <div class="filter-window">
                <div class="category mt-3">
                    <div class="d-flex p-2 cat-class">
                        <div class="col-7">Choose category</div>
                        <div class="col-5 text-right">
                            <span class="btn  btn-sm">
                                <i class="dripicons-cross"></i>
                            </span>
                        </div>
                    </div>
                    <div class="w-100 row ml-1 ">
                        @foreach ($lims_category_list as $category)
                            <div class="col-md-3 mt-3" data-category="{{ $category->id }}">
                                <div class="category-img text-center" data-category="{{ $category->id }}"
                                    wire:click="getProductByFilter({{ $category->id }},0);" class="cursor-pointer">
                                    @if ($category->image)
                                        <img src="{{ secure_url('images/category', $category->image) }}" />
                                    @else
                                        <img src="{{ secure_url('images/product/zummXD2dvAtI.png') }}" />
                                    @endif
                                    <p class="text-center">{{ $category->name }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="brand mt-3">
                    <div class="row ml-2 mr-2 px-2">
                        <div class="col-7">Choose brand</div>
                        <div class="col-5 text-right">
                            <span class="btn btn-default btn-sm">
                                <i class="dripicons-cross"></i>
                            </span>
                        </div>
                    </div>
                    <div class="row ml-2 mt-3">
                        @foreach ($lims_brand_list as $brand)
                            @if ($brand->image)
                                <div class="col-md-3 brand-img text-center" data-brand="{{ $brand->id }}">
                                    <img src="{{ secure_url('images/brand', $brand->image) }}" />
                                    <p class="text-center">{{ $brand->title }}</p>
                                </div>
                            @else
                                <div class="col-md-3 brand-img" data-brand="{{ $brand->id }}">
                                    <img src="{{ secure_url('images/product/zummXD2dvAtI.png') }}" />
                                    <p class="text-center">{{ $brand->title }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <button class="btn btn-block btn-primary"
                        id="category-filter">{{ trans('file.category') }}</button>
                </div>
                <div class="col-md-4 d-none">
                    <button class="btn btn-block btn-info" id="brand-filter">{{ trans('file.Brand') }}</button>
                </div>
                <div class="col-md-4 d-none">
                    <button class="btn btn-block btn-danger"
                        id="featured-filter">{{ trans('file.Featured') }}</button>
                </div>
                <div class="col-md-12 mt-1 table-container">
                    <table id="product-table" class="table no-shadow product-list">
                        <thead class="d-none">
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($product_number > 0)
                                @for ($i = 0; $i < ceil($product_number / 5); $i++)
                                    <tr>
                                        <td class="product-img sound-btn"
                                            title="{{ $lims_product_list[0 + $i * 5]->name }}"
                                            data-product ="{{ $lims_product_list[0 + $i * 5]->code . ' (' . $lims_product_list[0 + $i * 5]->name . ')' }}"
                                            wire:click="addToCart({{ json_encode($lims_product_list[0 + $i * 5]) }})">
                                            <img src="{{ url('images/product', $lims_product_list[0 + $i * 5]->base_image) }}"
                                                width="100%" />
                                            <p>{{ $lims_product_list[0 + $i * 5]->name }}</p>
                                            <span>{{ $lims_product_list[0 + $i * 5]->code }}</span>
                                        </td>
                                        @if (!empty($lims_product_list[1 + $i * 5]))
                                            <td class="product-img sound-btn"
                                                title="{{ $lims_product_list[1 + $i * 5]->name }}"
                                                data-product ="{{ $lims_product_list[1 + $i * 5]->code . ' (' . $lims_product_list[1 + $i * 5]->name . ')' }}"
                                                wire:click="addToCart({{ json_encode($lims_product_list[1 + $i * 5]) }})">
                                                <img src="{{ url('images/product', $lims_product_list[1 + $i * 5]->base_image) }}"
                                                    width="100%" />
                                                <p>{{ $lims_product_list[1 + $i * 5]->name }}</p>
                                                <span>{{ $lims_product_list[1 + $i * 5]->code }}</span>
                                            </td>
                                        @else
                                            <td style="border:none;"></td>
                                        @endif
                                        @if (!empty($lims_product_list[2 + $i * 5]))
                                            <td class="product-img sound-btn"
                                                title="{{ $lims_product_list[2 + $i * 5]->name }}"
                                                data-product ="{{ $lims_product_list[2 + $i * 5]->code . ' (' . $lims_product_list[2 + $i * 5]->name . ')' }}"
                                                wire:click="addToCart({{ json_encode($lims_product_list[2 + $i * 5]) }})">
                                                <img src="{{ url('images/product', $lims_product_list[2 + $i * 5]->base_image) }}"
                                                    width="100%" />
                                                <p>{{ $lims_product_list[2 + $i * 5]->name }}</p>
                                                <span>{{ $lims_product_list[2 + $i * 5]->code }}</span>
                                            </td>
                                        @else
                                            <td style="border:none;"></td>
                                        @endif
                                        @if (!empty($lims_product_list[3 + $i * 5]))
                                            <td class="product-img sound-btn"
                                                title="{{ $lims_product_list[3 + $i * 5]->name }}"
                                                data-product ="{{ $lims_product_list[3 + $i * 5]->code . ' (' . $lims_product_list[3 + $i * 5]->name . ')' }}"
                                                wire:click="addToCart({{ json_encode($lims_product_list[3 + $i * 5]) }})">
                                                <img src="{{ url('images/product', $lims_product_list[3 + $i * 5]->base_image) }}"
                                                    width="100%" />
                                                <p>{{ $lims_product_list[3 + $i * 5]->name }}</p>
                                                <span>{{ $lims_product_list[3 + $i * 5]->code }}</span>
                                            </td>
                                        @else
                                            <td style="border:none;"></td>
                                        @endif
                                        @if (!empty($lims_product_list[4 + $i * 5]))
                                            <td class="product-img sound-btn"
                                                title="{{ $lims_product_list[4 + $i * 5]->name }}"
                                                data-product ="{{ $lims_product_list[4 + $i * 5]->code . ' (' . $lims_product_list[4 + $i * 5]->name . ')' }}"
                                                wire:click="addToCart({{ json_encode($lims_product_list[4 + $i * 5]) }})">
                                                <img src="{{ url('images/product', $lims_product_list[4 + $i * 5]->base_image) }}"
                                                    width="100%" />
                                                <p>{{ $lims_product_list[4 + $i * 5]->name }}</p>
                                                <span>{{ $lims_product_list[4 + $i * 5]->code }}</span>
                                            </td>
                                        @else
                                            <td style="border:none;"></td>
                                        @endif
                                    </tr>
                                @endfor
                            @else
                                <tr class="odd">
                                    <td valign="top" colspan="5" class="dataTables_empty">No data available in
                                        table</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>



    <!-- payment modal -->
    <div id="add-payment" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="exampleModalLabel"
        aria-hidden="true" class="modal fade text-left" wire:ignore.self>
        <div role="document" class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">{{ trans('file.Finalize Sale') }}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                            aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="spinner-border" role="status" wire:loading>
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="row">
                        <div class="col-md-10">
                            <div class="d-flex">
                                @if (in_array('card', $options))
                                    <div class="column-5 mr-2">
                                        <button style="background: #0984e3" type="button"
                                            wire:click="$set('payment_data.payment_method',3)"
                                            class="btn btn-sm btn-custom payment-btn" id="credit-card-btn"><i
                                                class="fa fa-credit-card"></i>
                                            {{ trans('file.Card') }}</button>
                                    </div>
                                @endif
                                @if (in_array('cash', $options))
                                    <div class="column-5 mr-2">
                                        <button style="background: #00cec9" type="button"
                                            wire:click="$set('payment_data.payment_method',1)"
                                            class="btn btn-sm btn-custom payment-btn" id="cash-btn"><i
                                                class="fa fa-money"></i> {{ trans('file.Cash') }}</button>
                                    </div>
                                @endif
                                @if (in_array('cheque', $options))
                                    <div class="column-5 mr-2">
                                        <button style="background-color: #fd7272" type="button"
                                            wire:click="$set('payment_data.payment_method',4)"
                                            class="btn btn-sm btn-custom payment-btn " id="cheque-btn"><i
                                                class="fa fa-money"></i> {{ trans('file.Cheque') }}</button>
                                    </div>
                                @endif
                                @if (in_array('gift_card', $options))
                                    <div class="column-5 mr-2">
                                        <button style="background-color: #5f27cd" type="button"
                                            wire:click="$set('payment_data.payment_method',2)"
                                            class="btn btn-sm btn-custom payment-btn" id="gift-card-btn"><i
                                                class="fa fa-credit-card-alt"></i>
                                            {{ trans('file.Gift Card') }}</button>
                                    </div>
                                @endif
                                @if (in_array('deposit', $options))
                                    <div class="column-5 mr-2">
                                        <button style="background-color: #b33771" type="button"
                                            wire:click="$set('payment_data.payment_method',6)"
                                            class="btn btn-sm btn-custom payment-btn " id="deposit-btn"><i
                                                class="fa fa-university"></i>
                                            {{ trans('file.Deposit') }}</button>
                                    </div>
                                @endif

                                @foreach ($custom_methods as $cm)
                                    <div class="column-5 mr-2">
                                        <button style="background-color: #391e6b" type="button"
                                            class="btn btn-sm btn-custom payment-btn cmethod "
                                            wire:click="$set('payment_data.payment_method','{{ $cm->name }}')"
                                            data-cmethod ="{{ $cm->name }}">
                                            {{ $cm->name }}</button>
                                    </div>
                                @endforeach
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-3 mt-1">
                                    <label>{{ trans('file.Received Amount') }} *</label>
                                    @if($changedAmount)
                                    <input type="text" id="paying_amount" name="paying_amount"
                                        class="form-control numkey" required step="any"
                                        wire:change="changeCash($event.target.value)" />
                                    @else
                                    <input type="text" id="paying_amount" name="paying_amount"
                                        class="form-control numkey" required step="any"
                                        wire:model.live="payment_data.paying_amount"
                                        wire:change="$set('changedAmount',false)" />
                                    @endif
                                </div>
                                <div class="col-md-3 mt-1">
                                    <label>{{ trans('file.Paying Amount') }} *</label>
                                    <input type="text" name="paid_amount" class="form-control numkey"
                                        step="any" wire:model="payment_data.paid_amount">
                                </div>
                                <div class="col-md-3 mt-1">
                                    <label>{{ trans('file.Change') }} : </label>
                                    <p id="change" class="ml-2">
                                        {{ number_format($payment_data['change'], $general_setting->decimal, '.', '') }}
                                    </p>
                                </div>
                                <div class="col-md-3 mt-1 ">
                                    <input type="hidden" name="paid_by_id">
                                    <label>{{ trans('file.Paid By') }}</label>
                                    <select name="paid_by_id_select" class="form-control"
                                        wire:model.live="payment_data.payment_method">
                                        @if (in_array('cash', $options))
                                            <option value="1">Cash</option>
                                        @endif
                                        @if (in_array('gift_card', $options))
                                            <option value="2">Gift Card</option>
                                        @endif
                                        @if (in_array('card', $options))
                                            <option value="3">Credit Card</option>
                                        @endif
                                        @if (in_array('cheque', $options))
                                            <option value="4">Cheque</option>
                                        @endif
                                        @if (in_array('paypal', $options) &&
                                                strlen(env('PAYPAL_LIVE_API_USERNAME')) > 0 &&
                                                strlen(env('PAYPAL_LIVE_API_PASSWORD')) > 0 &&
                                                strlen(env('PAYPAL_LIVE_API_SECRET')) > 0)
                                            <option value="5">Paypal</option>
                                        @endif
                                        @if (in_array('deposit', $options))
                                            <option value="6">Deposit</option>
                                        @endif
                                        @if ($lims_reward_point_setting_data && $lims_reward_point_setting_data->is_active)
                                            <option value="7">Points</option>
                                        @endif

                                        @foreach ($custom_methods as $cm)
                                            <option value="{{ $cm->name }}">{{ $cm->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($payment_data['payment_method'] == 2)
                                    <div class="form-group col-md-12 gift-card">
                                        <label> {{ trans('file.Gift Card') }} *</label>
                                        <input type="hidden" name="gift_card_id">
                                        <select id="gift_card_id_select" name="gift_card_id_select"
                                            class="form-control" data-live-search="true"
                                            wire:model="payment_data.gift_card_id" data-live-search-style="begins"
                                            title="Select Gift Card..." required>
                                            <option value="">--Select--</option>
                                            @foreach ($gift_card as $card)
                                                <option value="{{ $card->id }}">{{ $card->card_no }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                @if ($payment_data['payment_method'] == 3)
                                    <div class="form-group col-md-12 mt-3">
                                        <div class="card-element form-control">
                                        </div>
                                        <div class="card-errors" role="alert"></div>
                                    </div>
                                @endif
                                @if ($payment_data['payment_method'] == 4)
                                    <div class="form-group col-md-12 cheque">
                                        <label>{{ trans('file.Cheque Number') }} *</label>
                                        <input type="text" name="cheque_no" wire:model="payment_data.cheque_no"
                                            class="form-control">
                                    </div>
                                @endif
                                <div class="form-group col-md-12">
                                    <label>{{ trans('file.Payment Note') }}</label>
                                    <textarea id="payment_note" rows="2" class="form-control" name="payment_note"
                                        wire:model="payment_data.payment_note"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>{{ trans('file.Sale Note') }}</label>
                                    <textarea rows="3" class="form-control" name="sale_note" wire:model="payment_data.sale_note"></textarea>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>{{ trans('file.Staff Note') }}</label>
                                    <textarea rows="3" class="form-control" name="staff_note" wire:model="payment_data.staff_note"></textarea>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button id="submit-btn" type="button" wire:click="saveCart(1)"
                                    wire:loading.attr="disabled"
                                    class="btn btn-primary">{{ trans('file.submit') }}</button>
                            </div>
                        </div>
                        @if ($payment_data['payment_method'] == 1)
                            <div class="col-md-2 qc" data-initial="1">
                                <h4><strong>{{ trans('file.Quick Cash') }}</strong></h4>
                                <button class="btn btn-block btn-primary qc-btn sound-btn" data-amount="10"
                                    wire:click="changeCash(10)" type="button">10</button>
                                <button class="btn btn-block btn-primary qc-btn sound-btn" data-amount="20"
                                    wire:click="changeCash(20)" type="button">20</button>
                                <button class="btn btn-block btn-primary qc-btn sound-btn" data-amount="50"
                                    wire:click="changeCash(50)" type="button">50</button>
                                <button class="btn btn-block btn-primary qc-btn sound-btn" data-amount="100"
                                    wire:click="changeCash(100)" type="button">100</button>
                                <button class="btn btn-block btn-primary qc-btn sound-btn" data-amount="500"
                                    wire:click="changeCash(500)" type="button">500</button>
                                <button class="btn btn-block btn-primary qc-btn sound-btn" data-amount="1000"
                                    wire:click="changeCash(1000)" type="button">1000</button>
                                <button class="btn btn-block btn-danger qc-btn sound-btn" data-amount="0" id="btnClear"
                                wire:click="clearCash()"
                                type="button">{{ trans('file.Clear') }}</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- order_discount modal -->
    <div id="order-discount-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true" class="modal fade text-left" wire:ignore.self>
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('file.Order Discount') }}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                            aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>{{ trans('file.Order Discount Type') }}</label>
                            <select id="order-discount-type" name="order_discount_type_select" class="form-control"
                                wire:model="order_discount_type">
                                <option value="Flat">{{ trans('file.Flat') }}</option>
                                <option value="Percentage">{{ trans('file.Percentage') }}</option>
                            </select>
                            <input type="hidden" name="order_discount_type">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>{{ trans('file.Value') }}</label>
                            <input type="text" name="order_discount_value" class="form-control numkey"
                                id="order-discount-val">
                            <input type="hidden" name="order_discount" class="form-control" id="order-discount"
                                onkeyup='saveValue(this);'>
                        </div>
                    </div>
                    <button type="button" name="order_discount_btn" class="btn btn-primary"
                        wire:click="$set('order_discount_value',$('#order-discount-val').val())"
                        data-dismiss="modal">{{ trans('file.submit') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- coupon modal -->
    <div id="coupon-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
        class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('file.Coupon Code') }}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                            aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        {{-- <select class="form-control" wire:model="coupon_code">
                            @foreach ($lims_coupon_list as $coupon)
                                <option value="{{$coupon->id}}">{{$coupon->code}}</option>
                            @endforeach
                        </select> --}}
                        <input type="text" id="coupon-code" class="form-control" wire:model="coupon_code"
                            placeholder="Type Coupon Code..." />
                    </div>
                    <button type="button" class="btn btn-primary coupon-check" wire:click="couponApply"
                        data-dismiss="modal">{{ trans('file.submit') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- order_tax modal -->
    <div id="order-tax" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
        class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('file.Order Tax') }}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                            aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" name="order_tax_rate">
                        <select class="form-control" name="order_tax_rate_select" id="order-tax-rate-select"
                            wire:model="order_tax_rate">
                            <option value="0">No Tax</option>
                            @foreach ($lims_tax_list as $tax)
                                <option value="{{ $tax->rate }}|{{ $tax->id }}">{{ $tax->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" name="order_tax_btn" wire:click="$refresh" class="btn btn-primary"
                        data-dismiss="modal">{{ trans('file.submit') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- shipping_cost modal -->
    <div id="shipping-cost-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('file.Shipping Cost') }}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                            aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" name="shipping_cost" class="form-control numkey"
                            wire:model="shipping_cost" id="shipping-cost-val" step="any">
                    </div>
                    <button type="button" name="shipping_cost_btn" class="btn btn-primary"
                        wire:click="$set('shopping_cost',$('#shipping-cost-val').val())"
                        data-dismiss="modal">{{ trans('file.submit') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- add customer modal -->
    <div id="addCustomer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
        class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(['route' => 'customer.store', 'method' => 'post', 'files' => true, 'id' => 'customer-form']) !!}
                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">{{ trans('file.Add Customer') }}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                            aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                    <p class="italic">
                        <small>{{ trans('file.The field labels marked with * are required input fields') }}.</small>
                    </p>
                    <div class="form-group">
                        <label>{{ trans('file.Customer Group') }} *</strong> </label>
                        <select required class="form-control selectpicker" name="customer_group_id">
                            @foreach ($lims_customer_group_all as $customer_group)
                                <option value="{{ $customer_group->id }}">{{ $customer_group->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ trans('file.name') }} *</strong> </label>
                        <input type="text" name="customer_name" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>{{ trans('file.Email') }}</label>
                        <input type="text" name="email" placeholder="example@example.com" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>{{ trans('file.Phone Number') }}</label>
                        <input type="text" name="phone_number" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>{{ trans('file.Address') }}</label>
                        <input type="text" name="address" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>{{ trans('file.City') }}</label>
                        <input type="text" name="city" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="pos" value="1">
                        <button type="button"
                            class="btn btn-primary customer-submit-btn">{{ trans('file.submit') }}</button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- recent transaction modal -->
    <div id="recentTransaction" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">{{ trans('file.Recent Transaction') }}
                        <div class="badge badge-primary">{{ trans('file.latest') }} 10</div>
                    </h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                            aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" href="#sale-latest" role="tab"
                                data-toggle="tab">{{ trans('file.Sale') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#draft-latest" role="tab"
                                data-toggle="tab">{{ trans('file.Draft') }}</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane show active" id="sale-latest">
                            <div class="table-responsive">
                                @if ($recent_sale)
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>{{ trans('file.date') }}</th>
                                                <th>{{ trans('file.reference') }}</th>
                                                <th>{{ trans('file.customer') }}</th>
                                                <th>{{ trans('file.grand total') }}</th>
                                                <th>{{ trans('file.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($recent_sale as $sale)
                                                <?php $customer = DB::table('customers')->find($sale->customer_id); ?>
                                                <tr>
                                                    <td>{{ date('d-m-Y', strtotime($sale->created_at)) }}</td>
                                                    <td>{{ $sale->reference_no }}</td>
                                                    <td>{{ $customer ? $customer->name : ' - ' }}</td>
                                                    <td>{{ $sale->grand_total }}</td>
                                                    <td>
                                                        <div class="btn-group">
                                                            @if (in_array('sales-edit', $all_permission))
                                                                <a href="{{ route('sales.edit', $sale->id) }}"
                                                                    class="btn btn-success btn-sm" title="Edit"><i
                                                                        class="dripicons-document-edit"></i></a>&nbsp;
                                                            @endif
                                                            @if (in_array('sales-delete', $all_permission))
                                                                {{ Form::open(['route' => ['sales.destroy', $sale->id], 'method' => 'DELETE']) }}
                                                                <button type="submit" class="btn btn-danger btn-sm"
                                                                    onclick="return confirmDelete()" title="Delete"><i
                                                                        class="dripicons-trash"></i></button>
                                                                {{ Form::close() }}
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane fade" id="draft-latest">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('file.date') }}</th>
                                            <th>{{ trans('file.reference') }}</th>
                                            <th>{{ trans('file.customer') }}</th>
                                            <th>{{ trans('file.grand total') }}</th>
                                            <th>{{ trans('file.action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recent_draft as $draft)
                                            <?php $customer = DB::table('customers')->find($draft->customer_id); ?>
                                            <tr>
                                                <td>{{ date('d-m-Y', strtotime($draft->created_at)) }}</td>
                                                <td>{{ $draft->reference_no }}</td>
                                                <td>{{ $customer->name }}</td>
                                                <td>{{ $draft->grand_total }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        @if (in_array('sales-edit', $all_permission))
                                                            <a href="{{ secure_url('sales/' . $draft->id . '/create') }}"
                                                                class="btn btn-success btn-sm" title="Edit"><i
                                                                    class="dripicons-document-edit"></i></a>&nbsp;
                                                        @endif
                                                        @if (in_array('sales-delete', $all_permission))
                                                            {{ Form::open(['route' => ['sales.destroy', $draft->id], 'method' => 'DELETE']) }}
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                onclick="return confirmDelete()" title="Delete"><i
                                                                    class="dripicons-trash"></i></button>
                                                            {{ Form::close() }}
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- add cash register modal -->
    <div id="cash-register-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                {!! Form::open(['route' => 'cashRegister.store', 'method' => 'post']) !!}
                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">{{ trans('file.Add Cash Register') }}
                    </h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                            aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                    <p class="italic">
                        <small>{{ trans('file.The field labels marked with * are required input fields') }}.</small>
                    </p>
                    <div class="row">
                        <div class="col-md-6 form-group warehouse-section">
                            <label>{{ trans('file.Warehouse') }} *</strong> </label>
                            <select required name="warehouse_id" class="selectpicker form-control"
                                data-live-search="true" data-live-search-style="begins"
                                title="Select warehouse...">
                                @foreach ($lims_warehouse_list as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>{{ trans('file.Cash in Hand') }} *</strong> </label>
                            <input type="number" step="any" name="cash_in_hand" required
                                class="form-control">
                        </div>
                        <div class="col-md-12 form-group">
                            <button type="submit" class="btn btn-primary">{{ trans('file.submit') }}</button>
                        </div>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- cash register details modal -->
    <div id="register-details-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">
                        {{ trans('file.Cash Register Details') }}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                            aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                    <p>{{ trans('file.Please review the transaction and payments.') }}</p>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-hover">
                                <tbody>
                                    <tr>
                                        <td>{{ trans('file.Cash in Hand') }}:</td>
                                        <td id="cash_in_hand" class="text-right">0</td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('file.Total Sale Amount') }}:</td>
                                        <td id="total_sale_amount" class="text-right"></td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('file.Total Payment') }}:</td>
                                        <td id="total_payment" class="text-right"></td>
                                    </tr>
                                    @if (in_array('cash', $options))
                                        <tr>
                                            <td>{{ trans('file.Cash Payment') }}:</td>
                                            <td id="cash_payment" class="text-right"></td>
                                        </tr>
                                    @endif
                                    @if (in_array('card', $options))
                                        <tr>
                                            <td>{{ trans('file.Credit Card Payment') }}:</td>
                                            <td id="credit_card_payment" class="text-right"></td>
                                        </tr>
                                    @endif
                                    @if (in_array('cheque', $options))
                                        <tr>
                                            <td>{{ trans('file.Cheque Payment') }}:</td>
                                            <td id="cheque_payment" class="text-right"></td>
                                        </tr>
                                    @endif
                                    @if (in_array('gift_card', $options))
                                        <tr>
                                            <td>{{ trans('file.Gift Card Payment') }}:</td>
                                            <td id="gift_card_payment" class="text-right"></td>
                                        </tr>
                                    @endif
                                    @if (in_array('deposit', $options))
                                        <tr>
                                            <td>{{ trans('file.Deposit Payment') }}:</td>
                                            <td id="deposit_payment" class="text-right"></td>
                                        </tr>
                                    @endif
                                    @if (in_array('paypal', $options) &&
                                            strlen(env('PAYPAL_LIVE_API_USERNAME')) > 0 &&
                                            strlen(env('PAYPAL_LIVE_API_PASSWORD')) > 0 &&
                                            strlen(env('PAYPAL_LIVE_API_SECRET')) > 0)
                                        <tr>
                                            <td>{{ trans('file.Paypal Payment') }}:</td>
                                            <td id="paypal_payment" class="text-right"></td>
                                        </tr>
                                    @endif
                                    @foreach ($custom_methods as $cmos)
                                        <tr>
                                            <td>{{ $cmos->name }}:</td>
                                            <td id="cmos{{ $cmos->id }}" class="text-right"></td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td>{{ trans('file.Total Sale Return') }}:</td>
                                        <td id="total_sale_return" class="text-right"></td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('file.Total Expense') }}:</td>
                                        <td id="total_expense" class="text-right"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ trans('file.Total Cash') }}:</strong></td>
                                        <td id="total_cash" class="text-right"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6" id="closing-section">
                            <form action="{{ route('cashRegister.close') }}" method="POST">
                                @csrf
                                <input type="hidden" name="cash_register_id">
                                <button type="submit"
                                    class="btn btn-primary">{{ trans('file.Close Register') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- today sale modal -->
    <div id="today-sale-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">{{ trans('file.Today Sale') }}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                            aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                    <p>{{ trans('file.Please review the transaction and payments.') }}</p>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-hover">
                                <tbody>
                                    <tr>
                                        <td>{{ trans('file.Total Sale Amount') }}:</td>
                                        <td class="total_sale_amount text-right"></td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('file.Cash Payment') }}:</td>
                                        <td class="cash_payment text-right"></td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('file.Credit Card Payment') }}:</td>
                                        <td class="credit_card_payment text-right"></td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('file.Cheque Payment') }}:</td>
                                        <td class="cheque_payment text-right"></td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('file.Gift Card Payment') }}:</td>
                                        <td class="gift_card_payment text-right"></td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('file.Deposit Payment') }}:</td>
                                        <td class="deposit_payment text-right"></td>
                                    </tr>
                                    @if (in_array('paypal', $options) &&
                                            strlen(env('PAYPAL_LIVE_API_USERNAME')) > 0 &&
                                            strlen(env('PAYPAL_LIVE_API_PASSWORD')) > 0 &&
                                            strlen(env('PAYPAL_LIVE_API_SECRET')) > 0)
                                        <tr>
                                            <td>{{ trans('file.Paypal Payment') }}:</td>
                                            <td class="paypal_payment text-right"></td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td>{{ trans('file.Total Payment') }}:</td>
                                        <td class="total_payment text-right"></td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('file.Total Sale Return') }}:</td>
                                        <td class="total_sale_return text-right"></td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('file.Total Expense') }}:</td>
                                        <td class="total_expense text-right"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ trans('file.Total Cash') }}:</strong></td>
                                        <td class="total_cash text-right"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- today profit modal -->
    <div id="today-profit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">{{ trans('file.Today Profit') }}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                            aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <select required name="warehouseId" class="form-control">
                                <option value="0">{{ trans('file.All Warehouse') }}</option>
                                @foreach ($lims_warehouse_list as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mt-2">
                            <table class="table table-hover">
                                <tbody>
                                    <tr>
                                        <td>{{ trans('file.Product Revenue') }}:</td>
                                        <td class="product_revenue text-right"></td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('file.Product Cost') }}:</td>
                                        <td class="product_cost text-right"></td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('file.Expense') }}:</td>
                                        <td class="expense_amount text-right"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ trans('file.Profit') }}:</strong></td>
                                        <td class="profit text-right"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@push('live-scripts')
    <script>
        console.log("poslive")
        // Function to save data to localStorage
        function saveDataToLocalStorage(data) {
            localStorage.setItem('orders', JSON.stringify(data));
            //$set('items', json_decode(data));
        }

        function togglePaymentOption(currentOption) {
            $(".card-element").hide();
            $(".card-errors").hide();
            $(".cheque").hide();
            $(".gift-card").hide();
            $('input[name="cheque_no"]').attr('required', false);
        }

        document.addEventListener('livewire:init', () => {

            function retrieveDataFromLocalStorage() {
                var data = localStorage.getItem('orders');
                return data ? JSON.parse(data) : null;
            }

            $('button').on('click', function() {
                var audio = $("#mysoundclip2")[0];
                audio.play();
                //console.log($(this).attr('id'));

                if ($(this).attr('id') === 'draft-btn' || $(this).attr('id') === 'setDfltPay') {
                    // console.log($(this).attr('id'));
                    // console.log(@this.get('items').length);
                    // console.log('{{ count($items) }}');
                    if (@this.get('items').length == 0) {
                        alert("Please insert product to order table!");
                        return false;
                    }

                    // if($(this).attr('id') == 'setDfltPay')
                    // {
                    //     console.log('pay');
                    //     @this.dispatch('saveCart',{ sale_status : 1});
                    // }

                    //@this.set('sale_status', 1);
                    if ($(this).attr('id') == 'draft-btn') {
                        //console.log('draft');
                        @this.dispatch('saveCart', {
                            sale_status: 3
                        });
                    }

                    // if ($(this).attr('id') == 'btnClear') {
                    //     console.log(@this.get('grand_total'));
                    //     @this.dispatch('clearCash');
                    // }


                    //@this.set('sale_status', 3);

                }
            });


            $('.customer-submit-btn').on("click", function() {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('customer.store') }}',
                    data: $("#customer-form").serialize(),
                    success: function(response) {
                        key = response['id'];
                        value = response['name'] + ' [' + response['phone_number'] + ']';
                        $('select[name="customer_id"]').append('<option value="' + key + '">' +
                            value +
                            '</option>');
                        // $('select[name="customer_id"]').val(key);
                        $('.selectpicker').selectpicker('refresh');
                        $("#addCustomer").modal('hide');
                    }
                });
            });

            $('#category-filter').on('click', function(e) {
                e.stopPropagation();
                $('.filter-window').show('slide', {
                    direction: 'right'
                }, 'fast');
                $('.category').show();
                $('.brand').hide();
            });

            $('.category-img').on('click', function() {
                var category_id = $(this).data('category');
                var brand_id = 0;

                $(".table-container").children().remove();

                // $.get('sales/getproduct/' + category_id + '/' + brand_id, function(data) {
                //     //console.log(data);
                //     //populateProduct(data);
                // });
            });


            // Retrieve data from localStorage when the component is mounted
            // Listen for updates to the data and save it to localStorage
            Livewire.on('dataUpdated', function(data) {
                saveDataToLocalStorage(data);
            });


        });

        document.addEventListener('livewire:initialized', () => {
            //alert('loaded');
            var counter = 0;
            // Livewire.hook('request', ({uri, options, payload, respond, succeed, fail}) => {
            //     succeed(({status, json}) => {
            //         // Do here what you have to do
            //         alert(status);
            //         $('#btnEdit' + @this.get('qtyIndex')).trigger('click');
            //     })
            // })

            // Livewire.hook('morph.added', (element) => {
            //     // console.log('morph added');
            //     // console.log(element);
            //     //console.log(@this.get('loaded'));
            //     //console.log(@this.get('qtyIndex'));
            //     if (counter == 0) {
            //         counter = @this.get('qtyIndex');
            //         console.log(element.el.tagName);

            //         /*if(element.el.tagName == 'TR')
            //         {
            //             console.log('has extra : {{ $hasExtra }} '+@this.get('hasExtra'))
            //             console.log('added : {{ $added }} ' +@this.get('added'))
            //             console.log('qty index : {{ $qtyIndex }} '+@this.get('qtyIndex'))

            //             // alert(@this.get('hasExtra'));
            //             // alert(@this.get('added'));
            //             // alert(@this.get('qtyIndex'));
            //             $('#btnEdit{{ $qtyIndex }}').trigger('click');
            //         }                    */

            //         // if(element.el == 'tr')
            //         // {
            //         //     $('#btnEdit{{ $qtyIndex }}').trigger('click');
            //         // }
            //         //if (@this.get('loaded') == true || (@this.get('hasExtra') && @this.get('added'))) {
            //         //console.log('has extra : '+@this.get('hasExtra'));
            //         //console.log('added : '+@this.get('added'));
            //         //console.log((@this.get('hasExtra') && @this.get('added')));
            //         // if ((@this.get('hasExtra') && @this.get('added'))) {
            //         //     $('#btnEdit' + @this.get('qtyIndex')).trigger('click');
            //         //     // @this.dispatch('open-qty-modal', {
            //         //     //     index: @this.get('qtyIndex')
            //         //     // })
            //         // }
            //     }
            // });
        });
        Livewire.on('coupon-application', function(message) {
            alert(message);
        });

        Livewire.on('gift-card-application', function(message) {
            alert(message);
        });

        Livewire.on('open-qty-modal', function(index) {
            // alert($('#editModal' + index.index).length);
            // $('#editModal' + index.index).modal('show');
            // console.log(index.index);
            //$('#btnEdit' + index.index).trigger('click');
            //alert(index);
            const myTimeout = setTimeout(function(){
                // console.log('has extra : {{ $hasExtra }} '+@this.get('hasExtra'))
                // console.log('added : {{ $added }} ' +@this.get('added'))
                // console.log('qty index : {{ $qtyIndex }} '+@this.get('qtyIndex'))
                $('#btnEdit'+@this.get('qtyIndex')).trigger('click');
            }, 1000);



            // alert(@this.get('hasExtra'));
            // alert(@this.get('added'));
            // alert(@this.get('qtyIndex'));

        });

        Livewire.on('close-qty-modal', function(index) {
            console.log(index.index);
            $('#editModal' + index.index).modal('hide');
            // @this.set('loaded', false);
            // @this.set('hasExtra', false);
            // @this.set('added', false);

        });



        $("li#notification-icon").on("click", function(argument) {
            $.get('notifications/mark-as-read', function(data) {
                $("span.notification-number").text(alert_product);
            });
        });



        $("#today-sale-btn").on("click", function(e) {
            e.preventDefault();
            $.ajax({
                url: 'sales/today-sale/',
                type: "GET",
                success: function(data) {
                    $('#today-sale-modal .total_sale_amount').text(data['total_sale_amount']);
                    $('#today-sale-modal .total_payment').text(data['total_payment']);
                    $('#today-sale-modal .cash_payment').text(data['cash_payment']);
                    $('#today-sale-modal .credit_card_payment').text(data['credit_card_payment']);
                    $('#today-sale-modal .cheque_payment').text(data['cheque_payment']);
                    $('#today-sale-modal .gift_card_payment').text(data['gift_card_payment']);
                    $('#today-sale-modal .deposit_payment').text(data['deposit_payment']);
                    $('#today-sale-modal .paypal_payment').text(data['paypal_payment']);
                    $('#today-sale-modal .total_sale_return').text(data['total_sale_return']);
                    $('#today-sale-modal .total_expense').text(data['total_expense']);
                    $('#today-sale-modal .total_cash').text(data['total_cash']);

                }
            });
            $('#today-sale-modal').modal('show');
        });

        $("#today-profit-btn").on("click", function(e) {
            e.preventDefault();
            calculateTodayProfit(0);
        });

        $("#today-profit-modal select[name=warehouseId]").on("change", function() {
            calculateTodayProfit($(this).val());
        });

        isCashRegisterAvailable({{ $warehouse_id }});

        function isCashRegisterAvailable(warehouse_id) {
            $.ajax({
                url: 'cash-register/check-availability/' + warehouse_id,
                type: "GET",
                success: function(data) {
                    if (data == 'false') {
                        $("#register-details-btn").addClass('d-none');
                        $('#cash-register-modal select[name=warehouse_id]').val(warehouse_id);

                        if (role_id <= 2)
                            $("#cash-register-modal .warehouse-section").removeClass('d-none');
                        else
                            $("#cash-register-modal .warehouse-section").addClass('d-none');

                        $('.selectpicker').selectpicker('refresh');
                        $("#cash-register-modal").modal('show');
                    } else
                        $("#register-details-btn").removeClass('d-none');
                }
            });
        }

        $("#point-btn").on("click", function() {
            $('select[name="paid_by_id_select"]').val(7);
            $('.selectpicker').selectpicker('refresh');
            $('div.qc').hide();
            hide();
            pointCalculation();
        });

        function pointCalculation() {
            paid_amount = $('input[name=paid_amount]').val();
            required_point = Math.ceil(paid_amount / reward_point_setting['per_point_amount']);
            if (required_point > points[$('#customer_id').val()]) {
                alert('Customer does not have sufficient points. Available points: ' + points[$('#customer_id').val()]);
            } else {
                $("input[name=used_points]").val(required_point);
            }
        }

        const warehouse_id = {{ $warehouse_id }}
        $("#register-details-btn").on("click", function(e) {
            e.preventDefault();
            $.ajax({
                url: 'cash-register/showDetails/' + warehouse_id,
                type: "GET",
                success: function(data) {
                    $('#register-details-modal #cash_in_hand').text(data['cash_in_hand']);
                    $('#register-details-modal #total_sale_amount').text(data['total_sale_amount']);
                    $('#register-details-modal #total_payment').text(data['total_payment']);
                    $('#register-details-modal #cash_payment').text(data['cash_payment']);
                    $('#register-details-modal #credit_card_payment').text(data['credit_card_payment']);
                    $('#register-details-modal #cheque_payment').text(data['cheque_payment']);
                    $('#register-details-modal #gift_card_payment').text(data['gift_card_payment']);
                    $('#register-details-modal #deposit_payment').text(data['deposit_payment']);
                    $('#register-details-modal #paypal_payment').text(data['paypal_payment']);
                    $('#register-details-modal #total_sale_return').text(data['total_sale_return']);
                    $('#register-details-modal #total_expense').text(data['total_expense']);
                    $('#register-details-modal #total_cash').text(data['total_cash']);
                    @foreach ($custom_methods as $cm)
                        $('#register-details-modal #cmos{{ $cm->id }}').text(data['cmos{{ $cm->id }}']);
                    @endforeach
                    $('#register-details-modal input[name=cash_register_id]').val(data['id']);
                }
            });
            $('#register-details-modal').modal('show');
        });

        /*document.addEventListener('DOMContentLoaded', function() {
            //alert('');
            Livewire.on('open-qty-modal', function(index) {
                alert($('#editModal' + index.index).length);
                $('#editModal' + index.index).modal('show');
                // console.log(index.index);
                //$('#btnEdit' + index.index).trigger('click');
                //alert(index);
            });
        });*/
        $(document).ready(function() {

        })
    </script>
@endpush
