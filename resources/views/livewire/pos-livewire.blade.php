<div class="container-fluid">
    <div class="row">
        <audio id="mysoundclip1" preload="auto">
            <source src="{{ url('beep/beep-timber.mp3') }}">
            </source>
        </audio>
        <audio id="mysoundclip2" preload="auto">
            <source src="{{ url('beep/beep-07.mp3') }}">
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
                                                    wire:model="customer_id" class="selectpicker form-control"
                                                    data-live-search="true" title="Select customer..."
                                                    style="width: 100px">
                                                    <?php
                                                    $deposit = [];
                                                    $points = [];
                                                    ?>
                                                    @foreach ($lims_customer_list as $customer)
                                                        @php
                                                            $deposit[$customer->id] =
                                                                $customer->deposit - $customer->expense;

                                                            $points[$customer->id] = $customer->points;
                                                        @endphp
                                                        <option {{ $customer_id == $customer->id ? 'selected' : '' }}
                                                            value="{{ $customer->id }}">
                                                            {{ $customer->name . ' (' . $customer->phone_number . ')' }}
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
                                            @foreach ($items as $item)
                                                <tr wire:key="tr-{{ time() }}">
                                                    <td class="col-sm-2 product-title">
                                                        <button type="button" class="edit-product btn btn-link"
                                                            data-toggle="modal" data-target="#editModal">
                                                            <span style="margin-left: -19px;">
                                                                <strong>{{ $item['name'] }}</strong>
                                                            </span>
                                                        </button>
                                                        <br>{{ $item['code'] }}
                                                        <p>In Stock: <span
                                                                class="in-stock">{{ $item['in_stock'] }}</span></p>
                                                    </td>
                                                    <td class="col-sm-2"><input type="text"
                                                            class="form-control batch-no" disabled="">
                                                        <input type="hidden" class="product-batch-id"
                                                            name="product_batch_id[]" value="{{ $item['batch'] }}">
                                                    </td>
                                                    <td class="col-sm-2 product-price">{{ $item['price'] }}</td>
                                                    <td class="col-sm-3">
                                                        <div class="input-group">
                                                            <span class="input-group-btn">
                                                                <button type="button" class="btn btn-default minus">
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
                                                                    class="btn btn-default plus"><span
                                                                        class="dripicons-plus"
                                                                        wire:click="plus({{ $loop->index }})"></span>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="col-sm-2 sub-total">{{ $item['total'] }}</td>
                                                    <td class="col-sm-1">
                                                        <button type="button" class="ibtnDel btn btn-danger btn-sm" 
                                                            wire:click="delete({{$loop->index}})"
                                                            wire:confirm.prompt="Are you sure?\n\nType DELETE to confirm|DELETE?">
                                                            <i class="dripicons-cross"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
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
                                    id="discount">{{ number_format($items->sum('discount'), $general_setting->decimal, '.', '') }}</span>
                            </div>
                            <div class="col-sm-4">
                                <span class="totals-title">{{ trans('file.Coupon') }} <button type="button"
                                        class="btn btn-link btn-sm" data-toggle="modal"
                                        data-target="#coupon-modal"><i
                                            class="dripicons-document-edit"></i></button></span><span
                                    id="coupon-text">{{ number_format($items->sum('coupon_discount'), $general_setting->decimal, '.', '') }}</span>
                            </div>
                            <div class="col-sm-4">
                                <span class="totals-title">{{ trans('file.Tax') }} <button type="button"
                                        class="btn btn-link btn-sm" data-toggle="modal" data-target="#order-tax"><i
                                            class="dripicons-document-edit"></i></button></span><span
                                    id="tax">{{ number_format($items->sum('tax'), $general_setting->decimal, '.', '') }}</span>
                            </div>
                            <div class="col-sm-4">
                                <span class="totals-title">{{ trans('file.Shipping') }} <button type="button"
                                        class="btn btn-link btn-sm" data-toggle="modal"
                                        data-target="#shipping-cost-modal"><i
                                            class="dripicons-document-edit"></i></button></span><span
                                    id="shipping-cost">{{ number_format(0, $general_setting->decimal, '.', '') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="payment-amount">
                    <h2>{{ trans('file.grand total') }} <span
                            id="grand-total">{{ number_format(0, $general_setting->decimal, '.', '') }}</span>
                    </h2>
                </div>
                <div class="payment-options">
                    @if (in_array('card', $options))
                        <div class="column-5">
                            <button style="background: #0984e3" type="button"
                                class="btn btn-sm btn-custom payment-btn" data-toggle="modal" id="setDfltPay"
                                data-target="#add-payment"><i class="fa fa-credit-card"></i>
                                {{ trans('file.Pay') }}</button>
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
                                data-target="#add-payment" id="paypal-btn"><i class="fa fa-paypal"></i>
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
                            @if ($alert_product_count + count(\Auth::user()->unreadNotifications) > 0)
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
                                        <a href="{{ url('my-transactions/' . date('Y') . '/' . date('m')) }}"><i
                                                class="dripicons-swap"></i>
                                            {{ trans('file.My Transaction') }}</a>
                                    </li>
                                    @if (Auth::user()->role_id != 5)
                                        <li>
                                            <a
                                                href="{{ url('holidays/my-holiday/' . date('Y') . '/' . date('m')) }}"><i
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
                                <div class="category-img text-center" data-category="{{ $category->id }}" wire:click="getProductByFilter({{ $category->id }},0);">
                                    @if ($category->image)
                                        <img src="{{ url('images/category', $category->image) }}" />
                                    @else
                                        <img src="{{ url('images/product/zummXD2dvAtI.png') }}" />
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
                                    <img src="{{ url('images/brand', $brand->image) }}" />
                                    <p class="text-center">{{ $brand->title }}</p>
                                </div>
                            @else
                                <div class="col-md-3 brand-img" data-brand="{{ $brand->id }}">
                                    <img src="{{ url('images/product/zummXD2dvAtI.png') }}" />
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
                            {{$product_number}}
                            @for ($i = 0; $i < ceil($product_number / 5); $i++)
                                <tr>
                                    <td class="product-img sound-btn"
                                        title="{{ $lims_product_list[0 + $i * 5]->name }}"
                                        data-product ="{{ $lims_product_list[0 + $i * 5]->code . ' (' . $lims_product_list[0 + $i * 5]->name . ')' }}">
                                        <img src="{{ url('images/product', $lims_product_list[0 + $i * 5]->base_image) }}"
                                            width="100%" />
                                        <p>{{ $lims_product_list[0 + $i * 5]->name }}</p>
                                        <span>{{ $lims_product_list[0 + $i * 5]->code }}</span>
                                    </td>
                                    @if (!empty($lims_product_list[1 + $i * 5]))
                                        <td class="product-img sound-btn"
                                            title="{{ $lims_product_list[1 + $i * 5]->name }}"
                                            data-product ="{{ $lims_product_list[1 + $i * 5]->code . ' (' . $lims_product_list[1 + $i * 5]->name . ')' }}">
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
                                            data-product ="{{ $lims_product_list[2 + $i * 5]->code . ' (' . $lims_product_list[2 + $i * 5]->name . ')' }}">
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
                                            data-product ="{{ $lims_product_list[3 + $i * 5]->code . ' (' . $lims_product_list[3 + $i * 5]->name . ')' }}">
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
                                            data-product ="{{ $lims_product_list[4 + $i * 5]->code . ' (' . $lims_product_list[4 + $i * 5]->name . ')' }}">
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
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@push('live-scripts')
    <script>
        // Function to save data to localStorage
        function saveDataToLocalStorage(data) {
            localStorage.setItem('orders', JSON.stringify(data));
            $set('items', json_decode(data));
        }


        document.addEventListener('livewire:init', () => {

            function retrieveDataFromLocalStorage() {
                var data = localStorage.getItem('orders');
                return data ? JSON.parse(data) : null;
            }

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

                $.get('sales/getproduct/' + category_id + '/' + brand_id, function(data) {
                    console.log(data);
                    //populateProduct(data);
                });
            });
            Livewire.on('initialized', function() {

            });

            // Retrieve data from localStorage when the component is mounted
            Livewire.hook('component.init', function() {
                Livewire.dispatch('dataRetrieved', retrieveDataFromLocalStorage());
            });

            // Listen for updates to the data and save it to localStorage
            Livewire.on('dataUpdated', function(data) {
                saveDataToLocalStorage(data);
            });
        });
    </script>
@endpush
