import { Injectable } from '@angular/core';
import { Observable, fromEvent, merge, of } from 'rxjs';
import { map } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class ConnectionService {

  public apponline: Observable<boolean>;

  constructor() {
    this.ConnectivityMonitor();
  }

  private ConnectivityMonitor() {
    if (!window || !navigator || !('onLine' in navigator)) return;

    this.apponline = merge(
      of(null),
      fromEvent(window, 'online'),
      fromEvent(window, 'offline')
    ).pipe(map(() => navigator.onLine))
  }
}
