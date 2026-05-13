(function () {
	'use strict';

	const config = window.bafAiPlanner || {};
	const form = document.querySelector('[data-baf-ai-planner-form]');

	if (!form) {
		return;
	}

	const status = document.querySelector('[data-baf-ai-planner-status]');
	const empty = document.querySelector('[data-baf-ai-planner-empty]');
	const success = document.querySelector('[data-baf-ai-planner-success]');
	const run = document.querySelector('[data-baf-ai-planner-run]');
	const title = document.querySelector('[data-baf-ai-planner-title]');
	const summary = document.querySelector('[data-baf-ai-planner-summary]');
	const brief = document.querySelector('[data-baf-ai-planner-brief]');
	const days = document.querySelector('[data-baf-ai-planner-days]');
	const opportunities = document.querySelector('[data-baf-ai-planner-opportunities]');
	const disclaimer = document.querySelector('[data-baf-ai-planner-disclaimer]');
	const draft = document.querySelector('[data-baf-ai-planner-draft]');
	const submit = form.querySelector('button[type="submit"]');
	const promptInput = form.querySelector('[name="prompt"]');
	const originInput = form.querySelector('[name="origin"]');
	const destinationInput = form.querySelector('[name="destination"]');
	const daysInput = form.querySelector('[name="days"]');
	const departInput = form.querySelector('[name="departure_date"]');
	const returnInput = form.querySelector('[name="return_date"]');
	const travelersInput = form.querySelector('[name="travelers"]');
	const travelStyleInput = form.querySelector('[name="travel_style"]');
	const handoffActionTypes = [
		['placement_card', 'Travelpayouts card'],
		['placement_draft', 'Placement draft'],
		['saved_trip_cta', 'Saved-trip CTA'],
		['alert_cta', 'Alert CTA'],
	];

	const clamp = (value, min, max) => Math.max(min, Math.min(max, Number.parseInt(value, 10) || min));
	const text = (value, fallback = '') => String(value || fallback).trim();

	const setStatus = (message, type) => {
		status.textContent = message || '';
		status.dataset.state = type || '';
	};

	const setLoading = (isLoading) => {
		form.classList.toggle('is-loading', isLoading);
		submit.disabled = isLoading || !config.canRunAi;
	};

	const calculateDaysFromDates = () => {
		if (!departInput.value || !returnInput.value) {
			return;
		}

		const depart = new Date(`${departInput.value}T00:00:00`);
		const back = new Date(`${returnInput.value}T00:00:00`);

		if (Number.isNaN(depart.getTime()) || Number.isNaN(back.getTime()) || back <= depart) {
			return;
		}

		const diff = Math.round((back - depart) / 86400000);
		daysInput.value = String(clamp(diff, 1, 21));
	};

	const makePayload = () => {
		const data = new FormData(form);
		const prompt = text(data.get('prompt'));
		const destination = text(data.get('destination'));
		const departureDate = text(data.get('departure_date'));
		const returnDate = text(data.get('return_date'));
		const travelers = clamp(data.get('travelers'), 1, 12);
		const dayCount = clamp(data.get('days'), 1, 21);
		const preferenceParts = [
			prompt,
			departureDate ? `Departure date: ${departureDate}` : '',
			returnDate ? `Return date: ${returnDate}` : '',
			`Travelers: ${travelers}`,
		].filter(Boolean);

		return {
			prompt,
			destination,
			origin: text(data.get('origin')),
			departure_date: departureDate,
			return_date: returnDate,
			days: dayCount,
			travelers,
			travel_style: text(data.get('travel_style'), 'balanced'),
			budget: text(data.get('budget')),
			preferences: preferenceParts.join('\n'),
			external_ai_consent: data.get('external_ai_consent') === '1',
			save: config.canEditContent && data.get('save_draft') === '1',
		};
	};

	const clearNode = (node) => {
		while (node.firstChild) {
			node.removeChild(node.firstChild);
		}
	};

	const appendPair = (label, value) => {
		if (!value) {
			return;
		}

		const term = document.createElement('dt');
		const detail = document.createElement('dd');
		term.textContent = label;
		detail.textContent = value;
		brief.append(term, detail);
	};

	const renderDays = (items) => {
		clearNode(days);

		if (!Array.isArray(items) || items.length === 0) {
			return;
		}

		const heading = document.createElement('h3');
		heading.textContent = 'Planning outline';
		days.appendChild(heading);

		items.forEach((item) => {
			const section = document.createElement('article');
			const itemTitle = document.createElement('h4');
			const itemSummary = document.createElement('p');
			const list = document.createElement('ul');

			section.className = 'baf-ai-planner__day';
			itemTitle.textContent = `Day ${item.day || ''}: ${text(item.title, 'Untitled day')}`;
			itemSummary.textContent = text(item.summary);

			(item.activities || []).forEach((activity) => {
				const entry = document.createElement('li');
				entry.textContent = `${text(activity.time_of_day, 'flexible')}: ${text(activity.title)}${activity.location ? ` (${activity.location})` : ''}`;
				list.appendChild(entry);
			});

			section.append(itemTitle, itemSummary, list);
			days.appendChild(section);
		});
	};

	const renderOpportunities = (items, tripPlan) => {
		clearNode(opportunities);

		if (!Array.isArray(items) || items.length === 0) {
			return;
		}

		const heading = document.createElement('h3');
		const list = document.createElement('ul');
		heading.textContent = 'Recommendation-only handoffs';

		items.forEach((item, index) => {
			const entry = document.createElement('li');
			const label = document.createElement('strong');
			const meta = document.createElement('span');
			const limitations = document.createElement('p');
			const metaParts = [
				text(item.vertical, 'travel'),
				text(item.recommendation_type, 'partner_handoff'),
				text(item.status, 'not_executed'),
				item.suggested_subid ? `SubID: ${text(item.suggested_subid)}` : '',
				item.confidence ? `Confidence: ${text(item.confidence)}` : '',
			].filter(Boolean);

			label.textContent = text(item.label, 'Travelpayouts opportunity');
			meta.textContent = metaParts.join(' - ');
			entry.append(label, meta);

			if (item.limitations) {
				limitations.textContent = text(item.limitations);
				entry.appendChild(limitations);
			}

			appendHandoffControls(entry, item, index, tripPlan);
			list.appendChild(entry);
		});

		opportunities.append(heading, list);
	};

	const appendHandoffControls = (entry, item, index, tripPlan) => {
		if (!config.canEditContent) {
			return;
		}

		const controls = document.createElement('div');
		const select = document.createElement('select');
		const approval = document.createElement('label');
		const checkbox = document.createElement('input');
		const button = document.createElement('button');
		const result = document.createElement('span');
		const hasDraft = tripPlan && tripPlan.id;
		const canPrepare = Boolean(hasDraft && config.providerRequestsAllowed);

		controls.className = 'baf-ai-planner__handoff-actions';
		select.setAttribute('aria-label', `Handoff type for ${text(item.label, 'opportunity')}`);

		handoffActionTypes.forEach(([value, labelText]) => {
			const option = document.createElement('option');
			option.value = value;
			option.textContent = labelText;
			select.appendChild(option);
		});

		checkbox.type = 'checkbox';
		checkbox.disabled = !canPrepare;
		approval.appendChild(checkbox);
		approval.appendChild(document.createTextNode(' Approve local handoff preparation with provider request consent.'));

		button.type = 'button';
		button.textContent = 'Prepare handoff';
		button.disabled = true;
		result.className = 'baf-ai-planner__handoff-status';

		if (!hasDraft) {
			result.textContent = 'Save as a Trip Plan draft before preparing a handoff.';
		} else if (!config.providerRequestsAllowed) {
			result.textContent = config.strings.handoffConsent;
		}

		checkbox.addEventListener('change', () => {
			button.disabled = !checkbox.checked || !canPrepare;
		});

		button.addEventListener('click', () => prepareHandoff({
			actionType: select.value,
			approved: checkbox.checked,
			button,
			opportunityIndex: index,
			result,
			tripPlanId: hasDraft ? tripPlan.id : 0,
		}));

		controls.append(select, approval, button, result);
		entry.appendChild(controls);
	};

	const renderDraft = (tripPlan) => {
		clearNode(draft);
		draft.hidden = true;

		if (!tripPlan || !tripPlan.id) {
			return;
		}

		const label = document.createElement('span');
		label.textContent = `Draft saved as Trip Plan #${tripPlan.id}. `;
		draft.appendChild(label);

		if (tripPlan.edit_url) {
			const link = document.createElement('a');
			link.href = tripPlan.edit_url;
			link.textContent = 'Open draft';
			draft.appendChild(link);
		}

		draft.hidden = false;
	};

	const renderResult = (payload) => {
		const itinerary = payload.itinerary || {};
		const tripBrief = payload.trip_brief || {};

		clearNode(brief);
		empty.hidden = true;
		success.hidden = false;
		run.textContent = `${text(payload.mode, 'demo')} mode - ${text(payload.provider, 'demo')} - Run ${text(payload.run_id).slice(0, 8)}`;
		title.textContent = text(itinerary.title, 'Trip brief');
		summary.textContent = text(itinerary.summary);

		appendPair('Destination', tripBrief.destination);
		appendPair('Origin', tripBrief.origin);
		appendPair('Dates', [tripBrief.departure_date, tripBrief.return_date].filter(Boolean).join(' to '));
		appendPair('Days', tripBrief.days ? `${tripBrief.days}` : '');
		appendPair('Travelers', tripBrief.travelers ? `${tripBrief.travelers}` : '');
		appendPair('Style', tripBrief.travel_style);
		appendPair('Budget', tripBrief.budget || 'Flexible');
		appendPair('External data', tripBrief.external_data_sent ? 'Sent to configured live provider after consent' : 'Not sent to a live provider');

		renderDays(itinerary.days);
		renderOpportunities(itinerary.affiliate_opportunities, payload.trip_plan);
		renderDraft(payload.trip_plan);
		disclaimer.textContent = text(itinerary.disclaimer);
	};

	const readError = async (response) => {
		try {
			const body = await response.json();
			return body && body.message ? body.message : config.strings.genericError;
		} catch (error) {
			return config.strings.genericError;
		}
	};

	const prepareHandoff = async ({ actionType, approved, button, opportunityIndex, result, tripPlanId }) => {
		if (!approved || !tripPlanId) {
			return;
		}

		button.disabled = true;
		result.textContent = 'Preparing local handoff...';

		try {
			const response = await fetch(config.handoffEndpoint, {
				method: 'POST',
				credentials: 'same-origin',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': config.nonce,
				},
				body: JSON.stringify({
					action_type: actionType,
					approved: true,
					opportunity_index: opportunityIndex,
					provider_request_consent: true,
					trip_plan_id: tripPlanId,
				}),
			});

			if (!response.ok) {
				throw new Error(await readError(response));
			}

			const body = await response.json();
			result.textContent = `${text(body.intent && body.intent.action_label, 'Handoff')} prepared. Provider action: ${text(body.provider_action, 'not_executed')}.`;
			button.textContent = 'Handoff prepared';
			setStatus(config.strings.handoffReady, 'success');
		} catch (error) {
			result.textContent = error.message || config.strings.genericError;
			button.disabled = false;
			setStatus(error.message || config.strings.genericError, 'error');
		}
	};

	departInput.addEventListener('change', calculateDaysFromDates);
	returnInput.addEventListener('change', calculateDaysFromDates);

	if (config.resumeTrip && config.resumeTrip.id) {
		promptInput.value = text(config.resumeTrip.planner_prompt, promptInput.value);
		originInput.value = text(config.resumeTrip.origin, originInput.value);
		destinationInput.value = text(config.resumeTrip.destination, destinationInput.value);
		departInput.value = text(config.resumeTrip.departure_date, departInput.value);
		returnInput.value = text(config.resumeTrip.return_date, returnInput.value);
		travelersInput.value = String(config.resumeTrip.travelers || travelersInput.value || 2);
		travelStyleInput.value = text(config.resumeTrip.travel_style, travelStyleInput.value || 'balanced');
		calculateDaysFromDates();
		setStatus(config.strings.resumeLoaded, 'success');
	}

	form.addEventListener('submit', async (event) => {
		event.preventDefault();

		if (!config.canRunAi) {
			setStatus(config.strings.capabilityRequired, 'error');
			return;
		}

		const payload = makePayload();

		if (!payload.destination || !payload.prompt) {
			setStatus('Add a prompt and destination before creating a trip brief.', 'error');
			return;
		}

		if (config.mode === 'live' && !config.liveReady) {
			setStatus(config.strings.liveNotReady || config.strings.genericError, 'error');
			return;
		}

		if (config.mode === 'live' && !payload.external_ai_consent) {
			setStatus(config.strings.consentRequired, 'error');
			return;
		}

		setLoading(true);
		setStatus(config.strings.loading, 'loading');

		try {
			const response = await fetch(config.endpoint, {
				method: 'POST',
				credentials: 'same-origin',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': config.nonce,
				},
				body: JSON.stringify(payload),
			});

			if (!response.ok) {
				throw new Error(await readError(response));
			}

			const responsePayload = await response.json();
			renderResult(responsePayload);
			setStatus(responsePayload.trip_plan && responsePayload.trip_plan.id ? config.strings.draftSaved : 'Trip brief ready. Review every field before using it in public content.', 'success');
		} catch (error) {
			setStatus(error.message || config.strings.genericError, 'error');
		} finally {
			setLoading(false);
		}
	});
})();
