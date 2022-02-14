import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { DebtPageRoutingModule } from './debt-routing.module';

import { DebtPage } from './debt.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    DebtPageRoutingModule
  ],
  declarations: [DebtPage]
})
export class DebtPageModule {}
