
                        @foreach ($tickets as $ticket)
                        <div class="ticket mb-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small>Ticket No. #{{ $ticket->id }}</small> | <small><i class="dripicons-calendar"></i> {{ date('d M Y', strtotime($ticket->created_at)) }}</small>
                                </div>
                                <div>
                                    <a class="btn" href="{{ url('superadmin/support/ticket/') }}/{{ $ticket->id }}"><i class="dripicons-preview"></i></a>
                                </div>
                            </div>
                            <a href="{{ url('superadmin/support/ticket/') }}/{{ $ticket->id }}">
                            <h3 class="mb-2">
                            {{ $ticket->subject }}
                            </h3>
                            </a>
                            <p class="short mb-0">{{ $ticket->description }}</p>
                        </div>
                        @endforeach
                    
