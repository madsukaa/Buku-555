import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { AdddebtPageRoutingModule } from './adddebt-routing.module';

import { AdddebtPage } from './adddebt.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    AdddebtPageRoutingModule
  ],
  declarations: [AdddebtPage]
})
export class AdddebtPageModule {}
