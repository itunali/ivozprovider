import EntityInterface from '@irontec/ivoz-ui/entities/EntityInterface';
import _ from '@irontec/ivoz-ui/services/translations/translate';
import FaxesInOut from '../FaxesInOut/FaxesInOut';
import Form from './Form';
import { foreignKeyGetter } from './foreignKeyGetter';

const FaxesIn: EntityInterface = {
  ...FaxesInOut,
  Form,
  foreignKeyGetter,
  localPath: '/faxes_out',
  title: _('Outgoing faxfile', { count: 2 }),
};

export default FaxesIn;
