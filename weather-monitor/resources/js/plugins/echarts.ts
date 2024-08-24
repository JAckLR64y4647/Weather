// Import the echarts core module, which provides the necessary interfaces for using echarts.
import * as echarts from 'echarts/core';

// Import bar charts, all suffixed with Chart
import { LineChart } from 'echarts/charts';

// Import the title, tooltip, rectangular coordinate system, dataset and transform components
import {
	TitleComponent,
	TooltipComponent,
	GridComponent,
	DatasetComponent,
	TransformComponent,
	LegendComponent, ToolboxComponent,
} from 'echarts/components';

// Features like Universal Transition and Label Layout
import { LabelLayout, UniversalTransition } from 'echarts/features';

// Import the Canvas renderer
// Note that including the CanvasRenderer or SVGRenderer is a required step
import { CanvasRenderer } from 'echarts/renderers';

// Register the required components
echarts.use([
	LineChart,
	TitleComponent,
	TooltipComponent,
	ToolboxComponent,
	GridComponent,
	DatasetComponent,
	TransformComponent,
	LegendComponent,
	LabelLayout,
	UniversalTransition,
	CanvasRenderer
]);

export default echarts;
